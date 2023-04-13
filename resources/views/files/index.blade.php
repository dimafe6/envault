<div>
    <div x-data="{ }" x-cloak class="bg-white shadow overflow-hidden rounded-md">
        <div class="bg-white px-4 py-5 sm:p-6 border-b border-gray-200">
            <div class="flex items-center justify-between flex-wrap sm:flex-no-wrap">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Secure Files
                    </h3>
                </div>

                <div>
                    <x-flash-message event="file.created" message="Added!"/>
                </div>
            </div>
        </div>

        <div class="sm:flex sm:flex-row-reverse sm:justify-between">
            <table class="min-w-full">
                <thead>
                <tr>
                    <th class="table-cell px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                        Filename
                    </th>

                    <th class="hidden sm:table-cell px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                        Uploaded date
                    </th>

                    <th class="hidden sm:table-cell px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                        Size
                    </th>

                    <th class="table-cell px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 text-center font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
                </thead>

                <tbody>
                @foreach ($files as $file)
                    <tr x-data="{ context: null }" x-cloak
                        wire:key="file.{{ $file->id }}">
                        <td class="sm:table-cell px-6 text-sm leading-5 text-gray-900">
                            {{ $file->name }}
                        </td>

                        <td class="hidden sm:table-cell px-6 text-sm leading-5 text-gray-500">
                            {{ $file->created_at->format('Y-m-d H:i:s') }}
                        </td>

                        <td class="hidden sm:table-cell px-6  text-sm leading-5 text-gray-500">
                            {{ $file->human_size }}
                        </td>

                        <td class="sm:table-cell py-2 whitespace-nowrap text-sm leading-5 text-gray-500">
                            <div class="px-4 sm:px-6 sm:flex sm:flex-row-reverse">
                                <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:ml-3 sm:w-auto">
                                    <button @click="context = 'delete'" type="button"
                                            class="inline-flex justify-center w-full rounded-md border border-transparent px-3 py-2 bg-red-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-red-500 focus:outline-none focus:border-red-700 focus:shadow-outline-red transition ease-in-out duration-150 sm:text-sm sm:leading-5"
                                    >
                                        <x-heroicon-s-trash class="h-4 w-4"/>
                                    </button>
                                </span>

                                <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:ml-3 sm:w-auto">
                                    <a @click="context = null"
                                       href="{{ route('apps.download_file',['app' => $app->id, 'token' => $app->existsToken()->token, 'uuid' => $file->uuid]) }}"
                                       wire:loading.class="opacity-75 cursor-wait"
                                       class="inline-flex justify-center w-full rounded-md border border-gray-300 px-3 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-indigo-300 focus:shadow-outline transition ease-in-out duration-150 sm:text-sm sm:leading-5"
                                    >
                                        <x-heroicon-s-download class="h-4 w-4"/>
                                    </a>
                                </span>
                            </div>

                            <div x-show.transition.opacity="context"
                                 class="fixed z-10 bottom-0 inset-x-0 px-4 pb-4 sm:inset-0 sm:flex sm:items-center sm:justify-center"
                            >
                                <div class="fixed inset-0">
                                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                                </div>

                                <div @click.away="context = null" @keydown.escape.window="context = null"
                                     class="relative z-10 sm:max-w-xl w-full"
                                >
                                    <div x-show="context == 'delete'" class="w-full sm:max-w-lg">
                                        @livewire('files.delete', ['file' => $file], key('file.'.$file->id.'.delete'))
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="4">
                        <div
                                class="leading-5 font-medium text-sm text-gray-800 bg-gray-50 truncate"
                                x-data="{ isUploading: false, progress: 0}"
                                x-on:livewire-upload-start="isUploading = true"
                                x-on:livewire-upload-finish="isUploading = false"
                                x-on:livewire-upload-error="isUploading = false"
                                x-on:livewire-upload-progress="progress = $event.detail.progress"
                        >
                            <form wire:submit.prevent="save">
                                <div class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto px-4 py-3 sm:px-6 sm:flex justify-center">
                                    @if (!$uploadedFile)
                                        <button x-show="!isUploading" @click="$refs.fileInput.click()" type="button"
                                                wire:loading.class="opacity-75 cursor-wait"
                                                class="inline-flex justify-center min-w rounded-md border border-gray-300 px-4
                                            py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm
                                            hover:text-gray-500 focus:outline-none focus:border-indigo-300
                                            focus:shadow-outline transition ease-in-out duration-150 sm:text-sm sm:leading-5"
                                        >
                                            <x-heroicon-s-upload class="h-5 w-5"/> Select file
                                        </button>
                                    @endif
                                    <input x-ref="fileInput"
                                           id="my-file-input"
                                           wire:model="uploadedFile"
                                           type="file"
                                           class="hidden"/>

                                    <div class="text-center mt-1 text-xs align-middle">
                                        @if ($uploadedFile)
                                            <p class="text-center px-3 py-3 text-2xl">{{ $uploadedFile->getClientOriginalName() }}</p>
                                            <button type="submit"
                                                    wire:loading.class="opacity-75 cursor-wait"
                                                    class="inline-flex justify-center rounded-md border border-gray-300 px-4
                                                        py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm
                                                        hover:text-gray-500 focus:outline-none focus:border-indigo-300
                                                        focus:shadow-outline transition ease-in-out duration-150 sm:text-sm sm:leading-5"
                                            >
                                                <x-heroicon-s-upload class="h-5 w-5"/> Upload
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <div class="bg-gray-50">
                                    <!-- Progress Bar -->
                                    <div x-show="isUploading" class="text-center">
                                        <progress max="100" x-bind:value="progress"></progress>
                                    </div>

                                    @error('uploadedFile') <div x-show="!isUploading" class="text-center text-red-600">{{ $message }}</div> @enderror

                                    <div id="fileLimitError" class="text-center text-red-600"
                                       style="display: none">
                                        The selected file exceeds the maximum allowed size of 100MB.
                                    </div>
                                </div>
                            </form>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.getElementById('my-file-input').addEventListener('change', function () {
            if (this.files[0].size > 100 * 1024 * 1024) {
                this.value = '';

                document.getElementById('fileLimitError').style.display = 'block';
            } else {
                document.getElementById('fileLimitError').style.display = 'none';
            }
        });
    </script>
@endpush

@livewireScripts
@stack('scripts')
