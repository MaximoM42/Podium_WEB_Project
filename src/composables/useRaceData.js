import { ref, watch, onMounted } from 'vue';
import { raceService } from '../services/raceService';

// Este composable acepta un 'ref' o 'computed' del categoryId como argumento
export const useRaceData = (categoryId) => {
    const racesWithPositions = ref([]);
    const loading = ref(true);
    const error = ref(null);

    const fetchRaces = async () => {
        if (!categoryId.value) return;
        
        try {
            loading.value = true;
            error.value = null;
            
            const response = await raceService.getByCategoryWithPositions(categoryId.value);
            
            // Transformar los datos del API al formato que espera el componente
            racesWithPositions.value = response.races.map(race => ({
                id: race.id,
                categoryId: race.category_id,
                name: race.name,
                location: race.location,
                dateR: race.date, // Formatear fecha si es necesario
                status: race.status,
                positions: race.positions.map(pos => ({
                    position: pos.position,
                    nick: pos.driver_name,
                    vehicleName: pos.vehicle_name || 'N/A',
                    time: pos.time_seconds,
                    points: pos.points
                }))
            }));
        } catch (err) {
            console.error("Error al cargar carreras:", err);
            error.value = "Error al cargar las carreras";
            racesWithPositions.value = [];
        } finally {
            loading.value = false;
        }
    };

    // Observar cambios en categoryId y recargar datos
    watch(categoryId, () => {
        fetchRaces();
    }, { immediate: true });

    onMounted(() => {
        fetchRaces();
    });

    // El composable devuelve la data ya procesada y lista para usar.
    return {
        racesWithPositions,
        loading,
        error,
        refetch: fetchRaces
    };
}