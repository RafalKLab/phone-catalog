import { ref, defineComponent, onMounted, watch } from 'vue'
import axios from 'axios'

export default defineComponent({
    name: 'CatalogPage',
    components: {
    },
    setup() {
        const selectedCapacity = ref('')
        const selectedBrand = ref('')
        const selectedModel = ref('')
        const selectedCategory = ref('')

        const capacities = ref([])
        const brands = ref([])
        const models = ref([])
        const categories = ref([])
        const minPrice = ref('')
        const maxPrice = ref('')


        const currentPage = ref(1)
        const itemsPerPage = ref(10)
        const totalItems = ref(0)
        const sortOption = ref('')

        const searchQuery = ref('')
        const items = ref([])
        const isLoading = ref(false)
        const error = ref(null)

        const formatPrice = (cents) => {
            return (cents / 100).toLocaleString('en-US', {
                style: 'currency',
                currency: 'PLN',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            })
        }

        const fetchItems = async () => {
            isLoading.value = true
            error.value = null
            try {
                const [sortField, sortDirection] = sortOption.value.split(':') || []

                const response = await axios.get('http://localhost:8000/api/items', {
                    params: {
                        page: currentPage.value,
                        itemsPerPage: itemsPerPage.value,

                        ...(selectedCategory.value && { 'category.id': selectedCategory.value }),
                        ...(selectedCapacity.value && { 'capacity.id': selectedCapacity.value }),
                        ...(selectedBrand.value && { 'model.brand.id': selectedBrand.value }),
                        ...(selectedModel.value && { 'model.id': selectedModel.value }),
                        ...(searchQuery.value && { 'model.name': searchQuery.value }),
                        ...(minPrice.value && { 'price[gte]': minPrice.value * 100 }),
                        ...(maxPrice.value && { 'price[lte]': maxPrice.value * 100 }),
                        ...(sortOption.value && { [`order[${sortField}]`]: sortDirection })
                    }

                })
                items.value = response.data['member']
                totalItems.value = response.data['totalItems']
            } catch (err) {
                console.error(err)
                error.value = 'Failed to load items.'
            } finally {
                isLoading.value = false
            }
        }

        const fetchFilters = async () => {
            try {
                const [capsRes, brandsRes, modelsRes, catsRes] = await Promise.all([
                    axios.get('http://localhost:8000/api/capacities'),
                    axios.get('http://localhost:8000/api/brands'),
                    axios.get('http://localhost:8000/api/models'),
                    axios.get('http://localhost:8000/api/categories')
                ])

                capacities.value = capsRes.data.member
                brands.value = brandsRes.data.member
                models.value = modelsRes.data.member
                categories.value = catsRes.data.member

            } catch (e) {
                console.error('Failed to fetch filters:', e)
            }
        }

        onMounted(() => {
            fetchItems()
            fetchFilters()
        })

        watch(
            () => [
                selectedCategory.value,
                selectedCapacity.value,
                selectedBrand.value,
                selectedModel.value,
                searchQuery.value,
                minPrice.value,
                maxPrice.value,
                sortOption.value
            ],
            () => {
                currentPage.value = 1
                fetchItems()
            }
        )

        watch(
            () => [currentPage.value, itemsPerPage.value],
            fetchItems
        )


        return {
            searchQuery,
            items,
            isLoading,
            error,
            currentPage,
            itemsPerPage,
            totalItems,
            formatPrice,

            // filter values
            selectedCategory,
            selectedCapacity,
            selectedBrand,
            selectedModel,

            // filter options
            capacities,
            brands,
            models,
            categories,
            minPrice,
            maxPrice,
            sortOption
        }
    }
})
