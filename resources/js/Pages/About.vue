<script setup>
import { useFilmFilters } from "../composables/useFilmFilters";
import AppLayout from "../layouts/AppLayout.vue";
// const {search} =
//     useFilmFilters(false);
</script>

<template>
    <AppLayout>
       <div class="max-w-6xl mx-auto flex flex-1 justify-center py-5">
          <div class="layout-content-container flex flex-col max-w-[960px] flex-1">
            <div class="flex flex-wrap justify-between gap-3 p-4">
              <p class=" tracking-light text-[32px] font-bold leading-tight min-w-72">About Festival Film Finder</p>
            </div>
            <h2 class=" tracking-light text-[28px] font-bold leading-tight px-4 text-left pb-3 pt-5">Welcome to Festival Film Finder</h2>
            <p class=" text-base font-normal leading-normal pb-3 pt-1 px-4">
              Festival Film Finder is your ultimate guide to discovering and exploring film festivals from around the world. Our platform brings you the latest information on
              various film festivals, providing you with access to film schedules, genres, awards, and the opportunity to explore countless films from emerging filmmakers and
              renowned directors alike. We aim to connect filmmakers, cinephiles, and movie enthusiasts by making it easier for you to discover festivals, learn about the films
              being showcased, and stay up to date on the film industry’s most exciting events.
            </p>
            <h2 class=" tracking-light text-[28px] font-bold leading-tight px-4 text-left pb-3 pt-5">What We Do</h2>
            <p class=" text-base font-normal leading-normal pb-3 pt-1 px-4">
              Our goal is simple: to make it easier for you to find the best film festivals happening worldwide. Whether you’re a filmmaker looking to submit your latest creation,
              or an audience member wanting to attend, our platform offers essential details for each festival, including: Festival Schedules: Find out when and where the film
              festivals are happening. Film Categories &amp; Genres: Explore films in different categories including drama, comedy, documentary, animation, and more. Festival
              Awards: Get information about the awards and recognitions presented at various festivals. Filmmaker Submissions: If you're a filmmaker, check out the submission
              process for festivals around the world.
            </p>
            <h2 class=" tracking-light text-[28px] font-bold leading-tight px-4 text-left pb-3 pt-5">Why Festival Film Finder?</h2>
            <p class=" text-base font-normal leading-normal pb-3 pt-1 px-4">
              Film festivals are a celebration of the art of filmmaking, providing a platform for filmmakers to showcase their creativity and connect with audiences. Whether you’re
              a first-time festival attendee or a seasoned film lover, Festival Film Finder ensures that you never miss an opportunity to experience these exciting events. We
              provide a user-friendly interface, rich in features and information that helps you navigate through the vibrant world of film festivals, discover films you wouldn’t
              typically encounter, and dive deeper into the stories that these festivals tell.
            </p>
            <h2 class=" tracking-light text-[28px] font-bold leading-tight px-4 text-left pb-3 pt-5">Our Mission</h2>
            <p class=" text-base font-normal leading-normal pb-3 pt-1 px-4">
              At Festival Film Finder, we are committed to: Promoting Diversity: Showcasing films from different cultures, genres, and filmmakers to promote diversity in cinema.
              Support for Filmmakers: Offering resources and tools for filmmakers to share their work with the world. Connecting Communities: Bridging the gap between festival
              organizers, filmmakers, and audiences for a more connected film ecosystem.
            </p>
            <h2 class=" tracking-light text-[28px] font-bold leading-tight px-4 text-left pb-3 pt-5">Get Involved</h2>
            <p class=" text-base font-normal leading-normal pb-3 pt-1 px-4">
              If you are passionate about cinema, whether you are a filmmaker, a festival organizer, or an avid movie lover, Festival Film Finder is the place to be. Stay
              connected, discover new festivals, and become part of a global film community. Join us today to explore the world of film festivals like never before.
            </p>
          </div>
        </div>
    </AppLayout>
</template>
