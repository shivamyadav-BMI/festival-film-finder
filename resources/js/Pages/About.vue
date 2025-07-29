<script setup>
import AppLayout from '../layouts/AppLayout.vue';


</script>

<template>
   <AppLayout>
    <section class="py-5 bg-light" id="about">
  <div class="container">
    <div class="row align-items-center mb-4">
      <div class="col-lg-6">
        <h2 class="display-5 fw-bold">About Film Festivals Finder</h2>
        <p class="lead text-muted">
          Film Festivals Finder is a smart, data-driven platform built to help cinephiles, curators, and filmmakers explore and analyze thousands of film festivals worldwide. With verified metadata, intelligent curation, and real-time updates, the project bridges the gap between scattered festival data and rich film discovery.
        </p>
      </div>
      <div class="col-lg-6">
        <img src="" alt="Film Festivals Finder" class="img-fluid rounded-3 shadow">
      </div>
    </div>

    <div class="row text-center">
      <div class="col-md-4 mb-4">
        <div class="p-4 bg-white rounded-3 shadow h-100">
          <i class="bi bi-film fs-2 text-primary mb-2"></i>
          <h5 class="fw-bold">Verified Film Data</h5>
          <p class="text-muted">Real-time enrichment from trusted sources like TMDb and IMDb ensures every film is real, accurate, and deduplicated.</p>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="p-4 bg-white rounded-3 shadow h-100">
          <i class="bi bi-table fs-2 text-success mb-2"></i>
          <h5 class="fw-bold">Excel-Ready Output</h5>
          <p class="text-muted">Clean, structured CSV/XLSX files ideal for researchers, curators, and programmers managing large film datasets.</p>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="p-4 bg-white rounded-3 shadow h-100">
          <i class="bi bi-globe2 fs-2 text-danger mb-2"></i>
          <h5 class="fw-bold">Global Festival Reach</h5>
          <p class="text-muted">Covers 1,500+ film festivals, from Cannes and Berlinale to niche independent showcases across the globe.</p>
        </div>
      </div>
    </div>

    <div class="row mt-5">
      <div class="col text-center">
        <h4 class="fw-semibold mb-3">Built For</h4>
        <ul class="list-inline">
          <li class="list-inline-item mx-3"><span class="badge bg-primary fs-6">Filmmakers</span></li>
          <li class="list-inline-item mx-3"><span class="badge bg-success fs-6">Curators</span></li>
          <li class="list-inline-item mx-3"><span class="badge bg-danger fs-6">Festival Programmers</span></li>
          <li class="list-inline-item mx-3"><span class="badge bg-secondary fs-6">Researchers</span></li>
        </ul>
      </div>
    </div>
  </div>
</section>

   </AppLayout>
</template>
