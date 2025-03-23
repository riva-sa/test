<section class="mx-xxl-5 mb-10">
    <div class="container-fluid">
        <div class="swiper-container clients mb-0 py-4"
            data-margin="30"
            data-dots="false"
            data-loop="true"
            data-autoplay="true"
            data-autoplaytime="1"
            data-drag="true"
            data-speed="3000"
            data-items-xxl="7"
            data-items-xl="6"
            data-items-lg="5"
            data-items-md="4"
            data-items-xs="4">
            <div class="swiper">
                <div class="swiper-wrapper ticker d-flex align-items-center">
                    @foreach($clients as $client)
                        <div class="swiper-slide px-10" wire:key="{{$client->id}}">
                            <div class="logo-wrapper">
                                <img src="{{ asset('storage/' . $client->logo) }}"
                                     alt="{{ $client->name }}"
                                     class="client-logo"/>
                            </div>
                        </div>
                    @endforeach
                    <!-- Repeat logos -->
                    @foreach($clients as $client)
                        <div class="swiper-slide px-10" wire:key="repeat-{{$client->id}}">
                            <div class="logo-wrapper">
                                <img src="{{ asset('storage/' . $client->logo) }}"
                                     alt="{{ $client->name }}"
                                     class="client-logo"/>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@push('styles')

<style>
    .logo-wrapper {
        width: 120px;  /* Set fixed width */
        height: 65px;  /* Set fixed height */
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .client-logo {
        max-width: 100%;
        max-height: 100%;
        width: auto;
        height: auto;
        object-fit: contain;
    }

    /* Optional: Add smooth animation */
    .ticker {
        animation: ticker 30s linear infinite;
    }

    @keyframes ticker {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }
    </style>
@endpush
