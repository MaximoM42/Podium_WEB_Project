import { ref } from "vue"

export const getCategories = () => {
    const categories = ref([
        {
            id: "TC",
            name: "Turismo Carretera",
            src: "/tc.jpg",
            rtr: "/categories/TC", //router
        },
        {
            id: "TCP",
            name: "Turismo Carretera Pista",
            src: "/tcp.jpg",
            rtr: "/categories/TCP", //router
        },
        {
            id: "TCPK",
            name: "Turismo Carretera Pick Ups",
            src: "/tcpk.jpg",
            rtr: "/categories/TCPK", //router
        },
    ])

    return {
        categories
    }
}