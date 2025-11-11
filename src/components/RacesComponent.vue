<script setup>
import { computed } from "vue";
import { useRoute } from "vue-router";
// 1. Importamos nuestro nuevo y poderoso composable
import { useRaceData } from '../composables/useRaceData';

// 2. Obtenemos la ruta actual para leer la URL
const route = useRoute();

// 3. Creamos una referencia computada al ID de la categoría desde la URL.
// Esto asegura que si la URL cambia, todo se actualizará reactivamente.
// route.params.id corresponde a la parte "TIPO" de "/categories/TIPO"
const categoryId = computed(() => route.params.id);

// 4. Usamos el composable, pasándole el ID de la categoría.
// ¡Y listo! 'racesWithPositions' ya contiene los datos filtrados y combinados.
const { racesWithPositions } = useRaceData(categoryId);
</script>

<template>
    <ul id="accordion">
        <template v-if="racesWithPositions.length > 0">
        <!-- 5. Iteramos directamente sobre el resultado del composable -->
            <li v-for="(race, index) in racesWithPositions" :key="race.id">
                
                <label :for="race.id">{{ race.dateR }} {{ race.name }}</label>
                <input type="radio" name="accordion" :id="race.id" :checked="index === 0">
                <div class="content">
                    <div v-if="race.positions.length > 0">
                        <table>
                            <thead>
                                <tr>
                                    <th>Pos.</th>
                                    <th>Piloto</th>
                                    <th>Vehículo</th>
                                    <th>Tiempo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="result in race.positions" :key="result.position">
                                    <td>{{ result.position }}°</td>
                                    <td>{{ result.nick }}</td>
                                    <td>{{ result.vehicleName }}</td>
                                    <td>{{ result.time }}s</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else>
                        <p>Resultados no disponibles para esta carrera.</p>
                    </div>
                </div>
            </li>
        </template>
        <template v-else>
            <li class="no-races-card">
                    <label>No hay carreras registradas hasta el momento.</label>
                </li>
        </template>
    </ul> 
</template>

<style scoped>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

#accordion {
    margin: 100px auto;
    width: clamp(320px, 90%, 700px);
    /* Ancho responsive */
    display: flex;
    flex-direction: column;
    gap: 15px;
    /* Espacio entre los elementos del acordeón */
}

#accordion li {
    list-style: none;
    width: 100%;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.05);
    /* Fondo de vidrio */
    backdrop-filter: blur(10px);
    /* El efecto esmerilado */
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
    overflow: hidden;
    /* Importante para que el contenido no se salga de los bordes redondeados */
    transition: all 0.3s ease;
}


#accordion li label {
    padding: 16px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 18px;
    font-weight: 500;
    cursor: pointer;
    color: #ffffff;
    /* Color de .card-content h2 */
    background-color: rgba(0, 0, 0, 0.5);
}

#accordion label+input[type="radio"] {
    display: none;
}

#accordion .content {
    padding: 0 20px;
    line-height: 26px;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.5s cubic-bezier(0, 1, 0, 1);
    color: #cdcdcd;
}
#accordion label+input[type="radio"]:checked+.content {
    max-height: 1000px;
    padding-bottom: 20px;
    transition: max-height 1s ease-in-out;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

th,
td {
    text-align: left;
    padding: 10px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

th {
    font-weight: bold;
    color: #ffffff;
}

tbody tr:last-child td {
    border-bottom: none;
}

#accordion li {
    list-style: none;
    width: 100%;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
    overflow: hidden;
    transition: all 0.3s ease;
}
</style>