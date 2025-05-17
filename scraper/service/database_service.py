import pymysql
import os

model_cache = {}
brand_cache = {}
capacity_cache = {}

def connect():
    return pymysql.connect(
        host='db',  # Docker service name
        user=os.getenv("MYSQL_USER", "root"),
        password=os.getenv("MYSQL_PASSWORD", ""),
        database=os.getenv("MYSQL_DATABASE", "yourdb"),
        charset='utf8mb4',
        cursorclass=pymysql.cursors.DictCursor
    )

def insert_brand(cursor, brand):
    if brand in brand_cache:
        return brand_cache[brand]

    cursor.execute("SELECT id FROM brands WHERE name=%s", (brand))
    result = cursor.fetchone()
    if result:
        brand_cache[brand] = result['id']

        return result['id']

    cursor.execute("INSERT INTO brands (name) VALUES (%s)", (brand))
    brand_id = cursor.lastrowid
    brand_cache[brand] = brand_id

    return brand_id

def insert_model(cursor, model_name, brand_id):
    if model_name in model_cache:
        return model_cache[model_name]

    cursor.execute("SELECT id FROM models WHERE name=%s", (model_name))
    result = cursor.fetchone()
    if result:
        model_cache[model_name] = result['id']

        return result['id']

    cursor.execute("INSERT INTO models (name, brand_id) VALUES (%s, %s)", (model_name,brand_id))
    model_id = cursor.lastrowid
    model_cache[model_name] = model_id

    return model_id

def insert_capacity(cursor, capacity):
    if capacity in capacity_cache:
        return capacity_cache[capacity]

    cursor.execute("SELECT id FROM capacities WHERE size=%s", (capacity))
    result = cursor.fetchone()
    if result:
        capacity_cache[capacity] = result['id']

        return result['id']

    cursor.execute("INSERT INTO capacities (size) VALUES (%s)", (capacity))
    capacity_id = cursor.lastrowid
    capacity_cache[capacity] = capacity_id

    return capacity_id

def insert_model_capacity(cursor, model_id, capacity_id):
    cursor.execute("""
        SELECT 1 FROM model_capacity WHERE model_id = %s AND capacity_id = %s
    """, (model_id, capacity_id))

    if not cursor.fetchone():
        cursor.execute("""
            INSERT INTO model_capacity (model_id, capacity_id)
            VALUES (%s, %s)
        """, (model_id, capacity_id))

def insert_category(cursor, category_name):
    cursor.execute("SELECT id FROM categories WHERE name=%s", (category_name))
    result = cursor.fetchone()
    if result:
        return result['id']

    cursor.execute("INSERT INTO categories (name) VALUES (%s)", (category_name))

    return cursor.lastrowid

def insert_item(cursor, model_id, category_id, item):
    cursor.execute("""
        INSERT INTO items (model_id, category_id, price, item_hash, grade)
        VALUES (%s, %s, %s, %s, %s)
    """, (model_id, category_id, item['price'], item['hash'], item['grade']))

def item_exists(cursor, item_hash: str) -> bool:
    cursor.execute("SELECT 1 FROM items WHERE item_hash = %s LIMIT 1", (item_hash))

    return cursor.fetchone() is not None


def insert_data(items, category_name="2nd-life-iphone"):
    connection = connect()
    try:
        with connection.cursor() as cursor:
            for item in items:
                if item_exists(cursor, item['hash']):
                                    continue

                brand_id = insert_brand(cursor, item['brand'])
                model_id = insert_model(cursor, item['model'], brand_id)
                capacity_id = insert_capacity(cursor, item['storage'])
                insert_model_capacity(cursor, model_id, capacity_id)
                category_id = insert_category(cursor, category_name)
                insert_item(cursor, model_id, category_id, item)

        connection.commit()
    finally:
        connection.close()
