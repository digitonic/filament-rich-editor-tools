<div>
    <div class="mt-5 mb-10">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div
                class="relative bg-primary-50 hover:bg-primary-100 overflow-hidden shadow-xs ring-1 ring-gray-900/5 sm:rounded-xl h-[480px] flex flex-col">
                <!-- Top Section with Logo and Name -->
                <div class="p-4 border-b border-gray-100">
                    <div class="flex items-center gap-x-4">
                        <x-mediatonic-filament::image
                            :media="1"
                            preset="original"
                            class="h-16 w-16 flex-none bg-gray-50"
                        />
                        <p class="text-xl font-semibold leading-6 text-gray-900">
                            Affiliate Review
                        </p>
                    </div>
                </div>

                <!-- Central Content Section - Flex grow to fill available space -->
                <div class="grow flex flex-col items-center px-4 py-3 overflow-y-auto">
                    <!-- Welcome Bonus -->
                    <div class="text-center mb-4">
                        <p class="text-lg font-semibold">USP Label</p>
                    </div>

                    <!-- Pros List -->
                    <div class="w-full mb-4">
                        <ul class="space-y-3">
                            <li class="flex items-start">
                                <svg class="h-5 w-5 shrink-0 text-green-600 mr-2 mt-1"
                                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor"
                                     style="min-width: 1.25rem; min-height: 1.25rem;">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="flex-1">This is a pro</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Buttons -->
                    <div class="flex flex-col items-center space-y-3 mt-auto mb-2">
                        <a href="#"
                           class="rounded-full bg-green-600 px-8 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-green-500 transition-all">
                            CTA Label
                        </a>
                        <a href="#"
                           class="text-sm text-gray-900 hover:underline">
                            Read Full Review
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
