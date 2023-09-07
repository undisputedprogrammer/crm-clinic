<x-easyadmin::app-layout>
<div >
    <div class=" flex flex-col flex-auto flex-shrink-0 antialiased bg-base-200  text-black ">

      <!-- Header -->
      <x-display.header/>

      {{-- page body --}}



      <div class=" h-fit pt-2 pb-3  bg-base-200 w-full ">

        <h1 class=" xl:px-12 font-semibold text-primary text-lg mb-5 mt-2">Manage Questions</h1>

        <x-tables.questions-table :questions="$questions"/>

      </div>

    </div>
</div>
<x-footer/>
</x-easyadmin::app-layout>
