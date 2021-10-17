<x-app-layout>
    <div class="container lg:w-3/4 md:w-4/5 w-11/12 mx-auto my-8 px-8 py-4 bg-white shadow-md">
        <x-flash-message :message="session('notice')" />
        <x-validation-errors :errors="$errors" />
        <article class="mb-2">
            <h2 class="font-bold font-sans break-normal text-gray-900 pt-6 pb-1 text-3xl md:text-4xl">
                {{ $walk->title }}</h2>
            <img src="{{ $walk->image_url }}" alt="image" class="mb-4">
            <p class="text-gray-700 text-base">{!! nl2br(e($walk->description)) !!}</p>
        </article>
        <div id="map" style="height: 70vh"></div>
        @auth
            <div class="flex flex-row text-center my-4">
                <a href="{{ route('walks.index') }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-20 mr-2">戻る</a>
                @can('update', $walk)
                    <a href="{{ route('walks.edit', $walk) }}"
                        class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-20 mr-2">編集</a>
                @endcan
                @can('delete', $walk)
                    <form action="{{ route('walks.destroy', $walk) }}" method="post">
                        @csrf
                        @method('DELETE')
                        <input type="submit" value="削除" onclick="if(!confirm('削除しますか？')){return false};"
                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-20">
                    </form>
                @endcan
            </div>
        @endauth
    </div>
    @include('partial.map')
    <script>
        L.marker([{{ $walk->latitude }}, {{ $walk->longitude }}])
            .bindPopup("{{ $walk->title }}", {
                closeButton: false
            })
            .addTo(map);
    </script>
</x-app-layout>
