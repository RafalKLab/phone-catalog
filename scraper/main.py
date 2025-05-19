# Start with docker compose run scraper scrape
import re
import requests
import hashlib
from bs4 import BeautifulSoup
from service.database_service import insert_data
from typing import Optional, Dict, List, Tuple

def extract_data(text: str) -> Optional[Dict[str, str | int]]:
    tokens = tokenize_product_text(text)
    hash = generate_item_hash_from_tokens(tokens)
    tokens = remove_junk_words(tokens)
    grade, tokens = extract_grade_from_tokens(tokens)
    price, tokens = extract_price_from_tokens(tokens)
    storage, tokens = extract_storage_from_tokens(tokens)
    brand, tokens = extract_brand_from_tokens(tokens)
    model, token = extract_model_from_tokens(tokens)

    # --- VALIDATION SECTION ---
    if not model.strip():
        return None

    if model.isdigit():
        return None

    required_fields = [brand, storage, grade, price]
    if any(field in [None, "", "Unknown", 0] for field in required_fields):
            return None
    # ---------------------------

    return {
          "brand": brand,
          "model": model,
          "storage": storage,
          "grade": grade,
          "price": price,
          "hash": hash
       }

def tokenize_product_text(text: str) -> List[str]:
    clean_text = text.replace('\xa0', ' ')
    tokens = clean_text.strip().split()  # Split by whitespace

    return tokens

def generate_item_hash_from_tokens(tokens: list[str]) -> str:
    joined = " ".join(tokens).lower().strip()

    return hashlib.sha256(joined.encode("utf-8")).hexdigest()

def remove_junk_words(tokens: List[str]) -> List[str]:
    """
    Removes predefined junk words (case-insensitive) from token list.
    """
    JUNK_WORDS = {"smartphone", "|", "retail", "login", "purchase_order_text", "used"}

    return [t for t in tokens if t.lower() not in JUNK_WORDS]

def extract_grade_from_tokens(tokens: List[str]) -> Tuple[str, List[str]]:
    """
    Extracts the grade letter following the word 'Grade', removes both from tokens.
    General rule: if we see 'Grade' and next token is a single letter, that's the grade.
    """
    grade = "Unknown"
    new_tokens = []
    i = 0

    while i < len(tokens):
        token = tokens[i]

        if token.lower() == "grade" and i + 1 < len(tokens):
            grade = tokens[i + 1].strip(",")
            i += 2

            continue


        new_tokens.append(token)
        i += 1

    return grade, new_tokens

def extract_price_from_tokens(tokens: List[str]) -> Tuple[int, List[str]]:
    """
    Extracts the price between 'price:' and 'PLN'.
    Returns price in cents (int).
    """
    price = 0
    new_tokens = []
    i = 0

    while i < len(tokens):
        token = tokens[i]

        if token.lower() == "price:":
            # Look for the closing 'pln'
            try:
                end_index = next(j for j in range(i + 1, len(tokens)) if tokens[j].lower() == "pln")
                raw_price_parts = tokens[i + 1:end_index]
                raw_price = ''.join(raw_price_parts).replace('\xa0', '').replace(' ', '')
                price_zl = float(raw_price)
                price = int(price_zl * 100) if price_zl > 0 else 0
                i = end_index + 1
                continue
            except (StopIteration, ValueError):
                price = 0
                i += 1
                continue

        new_tokens.append(token)
        i += 1

    return price, new_tokens

def extract_condition_from_tokens(tokens: List[str]) -> Tuple[str, List[str]]:
    """
    Extracts the last token as condition if it looks valid.
    Removes it from the token list.
    """
    if not tokens:
        return "Unknown", tokens

    last_token = tokens[-1].lower()

    return last_token.capitalize(), tokens[:-1]  # Remove condition token

def extract_storage_from_tokens(tokens: List[str]) -> Tuple[str, List[str]]:
    """
    Extracts the first token containing 'GB' (case-insensitive) as storage capacity.
    Removes it from the token list.
    """
    storage = "Unknown"
    new_tokens = []

    for token in tokens:
        if storage == "Unknown" and "gb" in token.lower():
            storage = token.upper()
        else:
            new_tokens.append(token)

    return storage, new_tokens

def extract_brand_from_tokens(tokens: List[str]) -> Tuple[str, List[str]]:
     """
     Looks for a known phone brand in the tokens. If found, returns the brand and tokens with it removed.
     Otherwise, returns 'Unknown' and the original tokens.
     """
     KNOWN_BRANDS = {
        "Apple", "Samsung", "Xiaomi", "OnePlus", "Huawei", "Nokia", "Motorola",
        "Sony", "Google", "Realme", "Oppo", "Vivo", "Asus", "Honor", "Lenovo"
     }

     lowered_tokens = [token.lower() for token in tokens]
     brand_lookup = {brand.lower(): brand for brand in KNOWN_BRANDS}
     for i, token in enumerate(lowered_tokens):
         if token in brand_lookup:
            brand = brand_lookup[token]
            new_tokens = tokens[:i] + tokens[i+1:]

            return brand, new_tokens

     return "Other", tokens

def extract_model_from_tokens(tokens: List[str]) -> Tuple[str, List[str]]:
    """
    All remaining tokens are treated as model.
    Removes commas from each token before joining.
    """
    cleaned_tokens = [t.replace(",", "") for t in tokens]

    return " ".join(cleaned_tokens).strip(), tokens

def extract_category_from_url(url: str) -> str:
    match = re.search(r"/category/([^/?#]+)", url)
    return match.group(1) if match else "Unknown"

def main():
    print("Scraping Breezy products")

    headers = {
            "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36",
            "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
            "Accept-Language": "en-US,en;q=0.5"
    }

    URL = "https://breezy.pl/en/category/2nd-life-iphone?page={}"
    CHUNK_SIZE = 200

    category_name = extract_category_from_url(URL)
    page = 1
    items = []

    response = requests.get(URL, headers=headers)

    while True:
        url = URL.format(page)
        print(f"Scraping page {page}...")

        try:
            response = requests.get(url, headers=headers)
        except requests.exceptions.RequestException as e:
            print(f"Request failed: {e}")
            break

        if response.status_code != 200:
            print(f"Page {page} returned status {response.status_code}. Assuming end of listing.")
            break

        soup = BeautifulSoup(response.text, 'html.parser')
        product_cards = soup.select("div.product-card-info.card__information")

        if not product_cards:
            print(f"No product cards on page {page}. Assuming end.")
            break

        for card in product_cards:
            raw = card.get_text(strip=True, separator=" | ")
            item = extract_data(raw)
            if item is None:
                continue

            items.append(item)

        if len(items) >= CHUNK_SIZE:
            print(f"Inserting chunk of {CHUNK_SIZE} items to DB")
            insert_data(items, category_name)
            items = []

        page += 1

    # Insert remaining items if any
    if items:
        print(f"Inserting final batch of {len(items)} items to DB")
        insert_data(items, category_name)


    print("Scraping complete.")

if __name__ == "__main__":
    main()
