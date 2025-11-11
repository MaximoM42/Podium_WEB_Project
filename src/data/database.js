// Este archivo exporta todos nuestros datos sin procesar.

export const races = [
    // Carreras de la categoría "TC"
    { id: "race1", categoryId: "TC", name: "RUS Grand Prix: San Luis Heat", dateR: "10/11/2025" },
    { id: "race2", categoryId: "TC", name: "Lusqtoff Black Grand Prix: Parana Heat", dateR: "15/11/2025" },
    { id: "race3", categoryId: "TC", name: "Shell V-Power Grand Prix: Buenos Aires Heat", dateR: "15/11/2025" },
    
    // Carreras de la categoría "TCP"
    { id: "race4", categoryId: "TCP", name: "RUS Grand Prix: San Luis Heat", dateR: "10/11/2025" },
    { id: "race5", categoryId: "TCP", name: "Lusqtoff Black Grand Prix: Parana Heat", dateR: "15/11/2025" },
    /*
    // Carreras de la categoría "TCPK"
    { id: "race4", categoryId: "TCPK", name: "Carrera de Pick Ups en La Plata", dateR: "20/10/2025" },*/
];

export const positions = [
    // Resultados de la carrera "race1" (TC)
    { raceId: "race1", position: 1, nick: "Maximo", time: 10, vehicleName: "Toyota Camry" },
    { raceId: "race1", position: 2, nick: "Ezequiel", time: 20, vehicleName: "Chevrolet Camaro" },
    
    // Resultados de la carrera "race2" (TC)
    { raceId: "race2", position: 1, nick: "Laura", time: 12, vehicleName: "Torino" },
    { raceId: "race2", position: 2, nick: "Carlos", time: 18, vehicleName: "Dodge Challenger" },
    
    { raceId: "race3", position: 1, nick: "Ana", time: 15, vehicleName: "Ford Falcon" },
    { raceId: "race3", position: 2, nick: "Juan", time: 30, vehicleName: "Chevrolet Chevy" },
    
    // Resultados de la carrera "race3" (TCP)
    { raceId: "race4", position: 1, nick: "Marcos", time: 8, vehicleName: "Dodge GTX" },
    { raceId: "race4", position: 2, nick: "Claudio", time: 20, vehicleName: "Torino" },

    { raceId: "race5", position: 1, nick: "Sergio", time: 10, vehicleName: "Ford Falcon" },
    { raceId: "race5", position: 2, nick: "Marcos", time: 50, vehicleName: "Dodge GTX" },
];