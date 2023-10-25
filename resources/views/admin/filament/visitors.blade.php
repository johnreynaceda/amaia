<div class="ml-3">
    <x-button label="View Visitor" wire:click="$set('view_visitor', true)" sm icon="eye" dark />

    <x-modal wire:model.defer="view_visitor" align="center">
        <x-card title="Registered Visitors">
            <p class="text-gray-600">
                {{ $getRecord()->visitors }}
            </p>
            <x-slot name="footer">
                <div class="flex justify-end gap-x-4">
                    <x-button flat label="Cancel" x-on:click="close" />
                </div>
            </x-slot>
        </x-card>
    </x-modal>
</div>
