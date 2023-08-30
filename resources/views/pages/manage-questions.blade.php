<x-easyadmin::app-layout>
<div >
    <div class=" flex flex-col flex-auto flex-shrink-0 antialiased bg-base-100  text-black ">

      <!-- Header -->
      <x-display.header/>

      {{-- page body --}}



      <div class=" h-[calc(100vh-3.5rem)] pt-7 pb-3  bg-base-200 w-full ">

        <h1 class=" text-center font-semibold text-primary text-lg mb-2">Manage Questions</h1>

        <x-tables.questions-table :questions="$questions"/>

      </div>

    </div>
</div>
</x-easyadmin::app-layout>
