import { ref, defineComponent, onMounted, watch } from 'vue'
import apiClient from '@/api/axios'

export default defineComponent({
    name: 'CatalogPage',
    setup() {
        // Filter states
        const selectedCategory = ref('')
        const selectedCapacity = ref('')
        const selectedBrand = ref('')
        const selectedModel = ref('')
        const minPrice = ref('')
        const maxPrice = ref('')

        // Filter options
        const categories = ref([])
        const capacities = ref([])
        const brands = ref([])
        const models = ref([])

        // Items and pagination
        const items = ref([])
        const currentPage = ref(1)
        const itemsPerPage = ref(10)
        const totalItems = ref(0)

        // Search and sort
        const searchQuery = ref('')
        const sortOption = ref('')

        // UI states
        const isLoading = ref(false)
        const error = ref(null)

        const formatPrice = (cents) =>
            (cents / 100).toLocaleString('en-US', {
                style: 'currency',
                currency: 'PLN',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            })

        const buildQueryParams = () => {
            const [sortField, sortDirection] = sortOption.value.split(':') || []
            return {
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
        }

        const fetchItems = async () => {
            isLoading.value = true
            error.value = null
            try {
                const response = await apiClient.get('/items', {
                    params: buildQueryParams()
                })
                items.value = response.data.member
                totalItems.value = response.data.totalItems
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
                    apiClient.get('/capacities'),
                    apiClient.get('/brands'),
                    apiClient.get('/models'),
                    apiClient.get('/categories')
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
            [
                selectedCategory,
                selectedCapacity,
                selectedBrand,
                selectedModel,
                searchQuery,
                minPrice,
                maxPrice,
                sortOption
            ],
            () => {
                currentPage.value = 1
                fetchItems()
            }
        )

        watch([currentPage, itemsPerPage], fetchItems)

        return {
            // UI
            isLoading,
            error,
            formatPrice,

            // Items & pagination
            items,
            currentPage,
            itemsPerPage,
            totalItems,

            // Filters (state)
            selectedCategory,
            selectedCapacity,
            selectedBrand,
            selectedModel,
            minPrice,
            maxPrice,

            // Filters (options)
            categories,
            capacities,
            brands,
            models,

            // Search & sort
            searchQuery,
            sortOption
        }
    }
})
