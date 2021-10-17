<x-app-layout>
    <div class="container max-w-7xl mx-auto px-4 md:px-12 pb-3 mt-3">
        <x-flash-message :message="session('notice')" />
        <form action="{{ route('walks.index') }}" method="get">
            <div class="shadow flex">
                <input name="keyword" class="w-full rounded p-2" type="text" placeholder="Search...">
                <button type="submit"
                    class="bg-white w-auto flex justify-end items-center text-blue-500 p-2 hover:text-blue-400">
                    <i class="material-icons">search</i>
                </button>
            </div>
        </form>
        <div class="flex flex-wrap -mx-1 lg:-mx-4 mb-4">
            @foreach ($walks as $walk)
                <article class="w-full px-4 md:w-1/4 text-xl text-gray-800 leading-normal">
                    <a href="{{ route('walks.show', $walk) }}">
                        <h2 class="font-bold font-sans break-normal text-gray-900 pt-6 pb-1 text-3xl md:text-4xl">
                            {{ $walk->title }}</h2>
                        <img class="w-full mb-2" src="{{ $walk->image_url }}" alt="image">
                        <p class="text-gray-700 text-base">{{ Str::limit($walk->description, 50) }}</p>
                    </a>
                </article>
            @endforeach
        </div>
        {{ $walks->links() }}
    </div>
</x-app-layout>
