<template>
    <div id="map" style="height: 400px; width: 100%;"></div>
  </template>
  
  <script>
  import L from "leaflet";
  
  export default {
    name: "MapComponent",
    props: {
      initialLocation: {
        type: Object,
        default: () => ({ lat: 13.6929, lng: -89.2182 }) // Coordenadas de San Salvador, El Salvador
      }
    },
    mounted() {
      this.$nextTick(() => {
        const mapContainer = document.getElementById("map");
        if (mapContainer) {
          // Crear el mapa centrado en la ubicación inicial
          this.map = L.map(mapContainer).setView([this.initialLocation.lat, this.initialLocation.lng], 13);
  
          // Agregar capa de mapa (usando OpenStreetMap)
          L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
          }).addTo(this.map);
  
          // Crear marcador arrastrable
          this.marker = L.marker([this.initialLocation.lat, this.initialLocation.lng], { draggable: true }).addTo(this.map);
  
          // Escuchar eventos de movimiento del marcador y emitir la nueva ubicación
          this.marker.on("moveend", (event) => {
            const latlng = event.target.getLatLng();
            this.$emit("location-selected", latlng); // Emitir la nueva ubicación seleccionada
          });
        } else {
          console.error("El contenedor del mapa no se encontró.");
        }
      });
    },
    beforeUnmount() {
      // Limpiar el mapa al desmontar el componente
      if (this.map) {
        this.map.remove();
      }
    }
  };
  </script>
  