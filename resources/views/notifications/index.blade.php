<x-app-layout>
    <div class="p-6">

        <h2 class="text-xl font-semibold text-white mb-4">
            🔔 Notifikasi
        </h2>

        <div class="bg-gray-800 rounded-lg shadow overflow-hidden">

            @forelse($notifs as $notif)
                <a href="{{ route('notif.read', $notif->id) }}"
                   class="block border-b border-gray-700 hover:bg-gray-700 transition">

                    <div class="p-4">

                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-white">
                                {{ $notif->title }}
                            </span>

                            <span class="text-sm text-gray-400">
                                {{ $notif->created_at->diffForHumans() }}
                            </span>
                        </div>

                        <p class="text-gray-300 mt-1">
                            {{ $notif->message }}
                        </p>

                        @if(!$notif->is_read)
                            <span class="inline-block mt-2 px-2 py-1 text-xs bg-blue-600 text-white rounded">
                                Belum dibaca
                            </span>
                        @endif

                    </div>

                </a>
            @empty
                <div class="p-6 text-center text-gray-400">
                    Tidak ada notifikasi
                </div>
            @endforelse

        </div>

    </div>
</x-app-layout>
