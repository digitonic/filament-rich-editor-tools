@if(isset($pros))
    <section aria-labelledby="affiliate-pros-cons" class="mt-6 pros-cons-section">
        <div class="relative md:bg-white">
            <div class="lg:grid lg:grid-cols-2 lg:gap-10">
                <div class="border border-green-600 rounded-2xl p-4 mt-5 lg:mt-0">
                    <div class="flex items-start">
                        <div class="mr-4 shrink-0 pt-1">
                            <svg class="h-8 w-8 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-xl font-semibold text-tertiary leading-none my-0">Pros</h4>
                            <div class="mt-3">
                                <ul class="custom-pros-list not-prose">
                                    @foreach($pros as $pro)
                                        <li class="text-gray-900 font-semibold custom-list-item">
                                                    <span>
                                                        <svg class="h-6 w-6 inline text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                                                          <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                                                        </svg>
                                                    </span>
                                            {{ $pro['text'] }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="border border-red-600 rounded-2xl p-4 mt-5 lg:mt-0">
                    <div class="flex items-start">
                        <div class="mr-4 shrink-0 pt-1">
                            <svg class="h-8 w-8 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-xl font-semibold text-tertiary leading-none my-0">Cons</h4>
                            <div class="mt-3">
                                <ul class="custom-cons-list not-prose">
                                    @foreach($cons as $con)
                                        <li class="text-gray-900 font-semibold custom-list-item">
                                                    <span>
                                                        <svg class="h-6 w-6 inline text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                                                          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                                        </svg>
                                                    </span>
                                            {{ $con['text'] }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
