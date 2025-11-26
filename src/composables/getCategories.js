import { ref, onMounted } from "vue"
import { categoryService } from "../services/categoryService"

export const getCategories = () => {
    const categories = ref([])
    const loading = ref(true)
    const error = ref(null)

    const fetchCategories = async () => {
        try {
            loading.value = true
            const response = await categoryService.getAll()
            
            // Transformar los datos del API al formato que espera el componente
            categories.value = response.categories.map(cat => ({
                id: cat.id,
                name: cat.name,
                src: cat.image_url || `/tc.jpg`,
                rtr: `/categories/${cat.id}`,
                description: cat.description
            }))
        } catch (err) {
            console.error("Error al cargar categorías:", err)
            error.value = "Error al cargar las categorías"
            
            // Fallback a datos locales en caso de error
            categories.value = [
                {
                    id: "TC",
                    name: "Turismo Carretera",
                    src: "/tc.jpg",
                    rtr: "/categories/TC",
                },
                {
                    id: "TCP",
                    name: "Turismo Carretera Pista",
                    src: "/tcp.jpg",
                    rtr: "/categories/TCP",
                },
                {
                    id: "TCPK",
                    name: "Turismo Carretera Pick Ups",
                    src: "/tcpk.jpg",
                    rtr: "/categories/TCPK",
                },
            ]
        } finally {
            loading.value = false
        }
    }

    onMounted(() => {
        fetchCategories()
    })

    return {
        categories,
        loading,
        error,
        refetch: fetchCategories
    }
}