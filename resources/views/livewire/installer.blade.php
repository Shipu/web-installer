<div class="bg-gray-50 min-h-screen flex items-center justify-center flex-col">
    <div class="flex p-8 mb-5 justify-center text-3xl text-sky-900 font-bold">
        {{ config('installer.name') }}
    </div>

    <form wire:submit.prevent="save" class="p-2 space-y-8 max-w-[85%] mx-auto">
        <div class="p-8 bg-white shadow">
            {{ $this->form }}
        </div>
    </form>
</div>