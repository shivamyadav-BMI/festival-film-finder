<script setup>
import AppLayout from "../layouts/AppLayout.vue";

</script>

<template>
    <AppLayout>
        <div class="px-40 flex flex-1 justify-center py-5">
            <div
                class="layout-content-container flex flex-col max-w-[960px] flex-1"
            >
                <div class="@container">
                    <div class="@[480px]:p-4">
                        <div
                            class="flex min-h-[480px] flex-col gap-6 bg-cover bg-center bg-no-repeat @[480px]:gap-8 @[480px]:rounded-lg items-center justify-center p-4"
                            style="
                                background-image: linear-gradient(
                                        rgba(0, 0, 0, 0.1) 0%,
                                        rgba(0, 0, 0, 0.4) 100%
                                    ),
                                    url('https://lh3.googleusercontent.com/aida-public/AB6AXuCd9V8UicFtEJSqk_Mosq6I4tXJz6UnpmhiHpeIYfTxwYF9jNY0gURuLxOVaB_1L86OkOlRT6yyLoXOvZOU47H0iy0hs88m8oerCBKS9AgaKsdDuWl9NubXP2XlAW7lIYeRqLvUODXw8sVCVlFDPbdssh1WyS8nf6EzCLEDP53t4dURtVfqVx-D7SubpkltXT5BmetN-CTDeHW-qNUfMmAMUR06cI5QatGALfTnDSxKBHZbpYPxx1h_XZEAnwHF0dwGIQDj2WEGLe0');
                            "
                        >
                            <div class="flex flex-col gap-2 text-center">
                                <h1
                                    class="text-white text-4xl font-black leading-tight tracking-[-0.033em] @[480px]:text-5xl @[480px]:font-black @[480px]:leading-tight @[480px]:tracking-[-0.033em]"
                                >
                                    Discover the World’s Greatest Festival Films
                                </h1>
                                <h2
                                    class="text-white text-sm font-normal leading-normal @[480px]:text-base @[480px]:font-normal @[480px]:leading-normal"
                                >
                                    Festival Film Finder is your curated gateway
                                    to explore verified, award-winning films
                                    from the world’s top film festivals. Built
                                    for cinephiles, critics, and curious viewers
                                    alike.
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>
                <p
                    class="text-white text-base font-normal leading-normal pb-3 pt-1 px-4"
                >
                    Festival Film Finder is a platform dedicated to showcasing
                    the best films from prestigious film festivals around the
                    globe. Our mission is to connect film enthusiasts with
                    critically acclaimed cinema, providing a curated experience
                    that highlights award-winning films and their achievements.
                    We believe that film festivals are a vital part of the
                    cinematic landscape, and we aim to make their discoveries
                    accessible to a wider audience. Our platform offers verified
                    film metadata, awards from top festivals, and ratings from
                    trusted sources like IMDb, Metacritic, and Rotten Tomatoes.
                    Users can also integrate their watchlists to keep track of
                    films they want to see. Join us in celebrating the art of
                    filmmaking and exploring the world's greatest festival
                    films.
                </p>
                <h2
                    class="text-white text-[22px] font-bold leading-tight tracking-[-0.015em] px-4 pb-3 pt-5"
                >
                    Features
                </h2>
                <div
                    class="grid grid-cols-[repeat(auto-fit,minmax(158px,1fr))] gap-3 p-4"
                >
                    <div
                        class="flex flex-1 gap-3 rounded-lg border border-[#314d68] bg-[#182634] p-4 items-center"
                    >
                        <div
                            class="text-white"
                            data-icon="FilmSlate"
                            data-size="24px"
                            data-weight="regular"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="24px"
                                height="24px"
                                fill="currentColor"
                                viewBox="0 0 256 256"
                            >
                                <path
                                    d="M216,104H102.09L210,75.51a8,8,0,0,0,5.68-9.84l-8.16-30a15.93,15.93,0,0,0-19.42-11.13L35.81,64.74a15.75,15.75,0,0,0-9.7,7.4,15.51,15.51,0,0,0-1.55,12L32,111.56c0,.14,0,.29,0,.44v88a16,16,0,0,0,16,16H208a16,16,0,0,0,16-16V112A8,8,0,0,0,216,104ZM192.16,40l6,22.07-22.62,6L147.42,51.83Zm-66.69,17.6,28.12,16.24-36.94,9.75L88.53,67.37Zm-79.4,44.62-6-22.08,26.5-7L94.69,89.4ZM208,200H48V120H208v80Z"
                                ></path>
                            </svg>
                        </div>
                        <h2
                            class="text-white text-base font-bold leading-tight"
                        >
                            Verified Film Metadata
                        </h2>
                    </div>
                    <div
                        class="flex flex-1 gap-3 rounded-lg border border-[#314d68] bg-[#182634] p-4 items-center"
                    >
                        <div
                            class="text-white"
                            data-icon="Trophy"
                            data-size="24px"
                            data-weight="regular"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="24px"
                                height="24px"
                                fill="currentColor"
                                viewBox="0 0 256 256"
                            >
                                <path
                                    d="M232,64H208V56a16,16,0,0,0-16-16H64A16,16,0,0,0,48,56v8H24A16,16,0,0,0,8,80V96a40,40,0,0,0,40,40h3.65A80.13,80.13,0,0,0,120,191.61V216H96a8,8,0,0,0,0,16h64a8,8,0,0,0,0-16H136V191.58c31.94-3.23,58.44-25.64,68.08-55.58H208a40,40,0,0,0,40-40V80A16,16,0,0,0,232,64ZM48,120A24,24,0,0,1,24,96V80H48v32q0,4,.39,8Zm144-8.9c0,35.52-28.49,64.64-63.51,64.9H128a64,64,0,0,1-64-64V56H192ZM232,96a24,24,0,0,1-24,24h-.5a81.81,81.81,0,0,0,.5-8.9V80h24Z"
                                ></path>
                            </svg>
                        </div>
                        <h2
                            class="text-white text-base font-bold leading-tight"
                        >
                            Awards from Top Festivals
                        </h2>
                    </div>
                    <div
                        class="flex flex-1 gap-3 rounded-lg border border-[#314d68] bg-[#182634] p-4 items-center"
                    >
                        <div
                            class="text-white"
                            data-icon="Star"
                            data-size="24px"
                            data-weight="regular"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="24px"
                                height="24px"
                                fill="currentColor"
                                viewBox="0 0 256 256"
                            >
                                <path
                                    d="M239.2,97.29a16,16,0,0,0-13.81-11L166,81.17,142.72,25.81h0a15.95,15.95,0,0,0-29.44,0L90.07,81.17,30.61,86.32a16,16,0,0,0-9.11,28.06L66.61,153.8,53.09,212.34a16,16,0,0,0,23.84,17.34l51-31,51.11,31a16,16,0,0,0,23.84-17.34l-13.51-58.6,45.1-39.36A16,16,0,0,0,239.2,97.29Zm-15.22,5-45.1,39.36a16,16,0,0,0-5.08,15.71L187.35,216v0l-51.07-31a15.9,15.9,0,0,0-16.54,0l-51,31h0L82.2,157.4a16,16,0,0,0-5.08-15.71L32,102.35a.37.37,0,0,1,0-.09l59.44-5.14a16,16,0,0,0,13.35-9.75L128,32.08l23.2,55.29a16,16,0,0,0,13.35,9.75L224,102.26S224,102.32,224,102.33Z"
                                ></path>
                            </svg>
                        </div>
                        <h2
                            class="text-white text-base font-bold leading-tight"
                        >
                            Ratings (IMDb, Metacritic, Rotten Tomatoes)
                        </h2>
                    </div>
                    <div
                        class="flex flex-1 gap-3 rounded-lg border border-[#314d68] bg-[#182634] p-4 items-center"
                    >
                        <div
                            class="text-white"
                            data-icon="Bookmark"
                            data-size="24px"
                            data-weight="regular"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="24px"
                                height="24px"
                                fill="currentColor"
                                viewBox="0 0 256 256"
                            >
                                <path
                                    d="M184,32H72A16,16,0,0,0,56,48V224a8,8,0,0,0,12.24,6.78L128,193.43l59.77,37.35A8,8,0,0,0,200,224V48A16,16,0,0,0,184,32Zm0,16V161.57l-51.77-32.35a8,8,0,0,0-8.48,0L72,161.56V48ZM132.23,177.22a8,8,0,0,0-8.48,0L72,209.57V180.43l56-35,56,35v29.14Z"
                                ></path>
                            </svg>
                        </div>
                        <h2
                            class="text-white text-base font-bold leading-tight"
                        >
                            Watchlist Integration
                        </h2>
                    </div>
                </div>
                <div class="w-full" style="height: 40px"></div>
            </div>
        </div>
    </AppLayout>
</template>
