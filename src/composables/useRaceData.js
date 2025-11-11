import { computed } from 'vue';
// Importamos los datos brutos de nuestro archivo de "base de datos"
import { races as allRaces, positions as allPositions } from '../data/database';

// Este composable acepta un 'ref' o 'computed' del categoryId como argumento
export const useRaceData = (categoryId) => {

    // Usamos una propiedad computada. Esto es muy potente: si el categoryId cambia,
    // Vue automáticamente recalculará esta variable y actualizará la vista.
    const racesWithPositions = computed(() => {
        // 1. Filtramos las carreras para obtener solo las de la categoría actual.
        // Usamos categoryId.value porque el argumento es un 'ref' o 'computed'.
        const filteredRaces = allRaces.filter(race => race.categoryId === categoryId.value);

        // 2. Mapeamos sobre las carreras filtradas para añadirles sus posiciones.
        return filteredRaces.map(race => {
            // Para cada carrera, filtramos todas las posiciones para encontrar las suyas.
            const racePositions = allPositions.filter(pos => pos.raceId === race.id);

            // Devolvemos un nuevo objeto combinando la carrera con sus posiciones.
            return {
                ...race,
                positions: racePositions
            };
        });
    });

    // El composable devuelve la data ya procesada y lista para usar.
    return {
        racesWithPositions
    };
}