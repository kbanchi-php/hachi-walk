<x-app-layout>
    <div class="container lg:w-1/2 md:w-4/5 w-11/12 mx-auto mt-8 px-8 bg-white shadow-md">
        <x-validation-errors :errors="$errors" />
        <h2 class="text-center text-lg font-bold pt-6 tracking-widest">食事記事編集</h2>
        <form action="{{ route('walks.update', $walk) }}" method="post" class="rounded pt-3 pb-8 mb-4">
            @csrf
            @method('patch')
            <div class="mb-4">
                <label class="block text-gray-700 text-sm mb-2" for="title">
                    タイトル
                </label>
                <input type="text" name="title"
                    class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full py-2 px-3"
                    required placeholder="タイトル" value="{{ old('title', $walk->title) }}">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm mb-2" for="category">
                    カテゴリー
                </label>
                @foreach ($categories as $category)
                    <div>
                        <label class="inline-flex items-center">
                            <input type="radio" class="form-radio" name="category_id"
                                value="{{ old('category_id', $category->id) }}"
                                {{ $category->id == $walk->category_id ? 'checked' : '' }}>
                            <span class="ml-2">{{ $category->name }}</span>
                        </label>
                    </div>
                @endforeach
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm mb-2" for="description">
                    詳細
                </label>
                <textarea name="description" rows="10"
                    class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full py-2 px-3"
                    required>{{ $walk->description }}</textarea>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm mb-2" for="image">
                    Photo
                </label>
                <div class="flex flex-wrap -mx-1 lg:-mx-4 mb-4">
                    @foreach ($walk->image_urls as $url)
                        <article class="w-full px-4 md:w-1/4 text-xl text-gray-800 leading-normal">
                            <img class="w-full mb-2" src="{{ $url }}" alt="image">
                        </article>
                    @endforeach
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm mb-2" for="map">
                    Map
                </label>
                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $walk->latitude) }}">
                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $walk->longitude) }}">
                <div id="map" style="height: 70vh"></div>
            </div>
            <input type="submit" value="更新"
                class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            <button type="button" onclick="location.href='{{ route('walks.show', $walk) }}'"
                class="w-full bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                戻る
            </button>
        </form>
    </div>
    @include('partial.map')
    <script>
        const lat = document.getElementById('latitude');
        const lng = document.getElementById('longitude');
        @if (!empty($walk))
            const marker = L.marker([{{ $walk->latitude }}, {{ $walk->longitude }}], {
            draggable: true
            }).bindPopup("{{ $walk->title }}", {closeButton: false}).addTo(map);
            lat.value = {{ $walk->latitude }};
            lng.value = {{ $walk->longitude }};
            marker.on('dragend', function(e) {
            // 座標は、e.target.getLatLng()で取得
            lat.value = e.target.getLatLng()['lat'];
            lng.value = e.target.getLatLng()['lng'];
            });
        @endif
    </script>
</x-app-layout>
