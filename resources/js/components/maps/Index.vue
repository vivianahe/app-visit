<template>
    <div class="container-fluid">
      <div class="row">
        <div class="col">
          <v-card class="mx-auto">
            <v-card-title class="title_card">
              <span>Visitas en mapa <span class="mdi mdi-map-marker-path"></span></span>
            </v-card-title>
            <v-card-text class="mb-5 p-0">
              <div class="map-container">
                <div id="map"></div>
              </div>
            </v-card-text>
          </v-card>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { onMounted, ref } from 'vue'
  import axios from 'axios'
  
  const dataVisit = ref([])
  
  onMounted(() => {
    getVisits()
  })
  
  const getMaps = () => {
    // Inicializa el mapa con un centro predeterminado
    const map = L.map('map').setView([4.5709, -74.2973], 6)
  
    // Agrega la capa de OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    }).addTo(map)
  
    // Recorre las ubicaciones y agrega los marcadores al mapa
    dataVisit.value.forEach((location) => {
      const { latitude, longitude, name, email } = location
      if (latitude && longitude) {
        L.marker([parseFloat(latitude), parseFloat(longitude)])
          .addTo(map)
          .bindPopup(`<b>${name}</b><br>${email}`)
      }
    })
  }
  
  const getVisits = async () => {
    try {
      const response = await axios.get("/getVisits") 
      dataVisit.value = response.data  
      getMaps()
    } catch (error) {
      console.error(error)
    }
  }
  </script>
  
  <style>
  #map {
    width: 100%;
    height: 100%;
  }
  
  .map-container {
    position: relative;
    width: 100%;
    height: 500px;
  }
  
  .v-card-text {
    padding: 0;
  }
  
  body, html, .container-fluid, .row, .col {
    height: 100%;
    margin: 0;
    padding: 0;
  }
  
  .v-card {
    height: 100%;
    display: flex;
    flex-direction: column;
  }
  </style>
  