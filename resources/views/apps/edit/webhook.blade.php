<div class="bg-white shadow overflow-hidden rounded-md">
    <form wire:submit.prevent="update" spellcheck="false">
        <div class="bg-white px-4 py-5 border-b border-gray-200 sm:px-6">
            <div class="flex items-center justify-between flex-wrap sm:flex-no-wrap">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Webhook call
                    </h3>
                </div>
                <div>
                    <x-flash-message event="app.webhook.set-up" message="Set up!"/>

                    <x-flash-message event="app.webhook.updated" message="Saved!"/>
                </div>
            </div>
        </div>
        <div class="px-4 py-6 sm:px-6">
            <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start">
                <label for="webhookUrl"
                       class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
                >
                    Webhook URL
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <div class="rounded-md shadow-sm">
                        <input wire:model.defer="webhook_url" id="webhookUrl" type="text"
                               placeholder="https://webhook.site/dfc86074-2c18-4ba8-8003-d110acb0bbbf"
                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('webhook_url') border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                        />
                    </div>
                    @error('webhook_url')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-4 py-4 sm:px-6 flex">
            <div class="inline-flex flex-shrink-0 items-center">
                <p class="inline-flex text-sm leading-5 font-medium text-{{$this->app->webhook_url ? 'green' : 'gray'}}-600">
                    <x-heroicon-s-information-circle class="mr-1 h-5 w-5 "/>
                    Webhook call {{ $this->app->webhook_url ? 'enabled.' : 'disabled.' }}
                </p>
            </div>
            <div class="flex w-full justify-end">
                <span class="inline-flex">
                    <button type="submit" wire:loading.class="opacity-75 cursor-wait"
                            class="inline-flex shadow-sm justify-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition duration-150 ease-in-out"
                    >
                        Save
                        <x-heroicon-s-check class="ml-1.5 -mr-1 h-5 w-5"/>
                    </button>
                </span>
            </div>
        </div>
    </form>
</div>
