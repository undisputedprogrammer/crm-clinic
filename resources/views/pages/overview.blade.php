<x-easyadmin::app-layout>
<div >
    <div class="min-h-screen flex flex-col flex-auto flex-shrink-0 antialiased bg-base-100  text-black ">

      <!-- Header -->
      <x-display.header/>
      <!-- ./Header -->



      <div class=" min-h-[calc(100vh-3.5rem)] w-full mx-auto  bg-base-100 mt-14  ">

        <div class="w-[96%] mx-auto rounded-xl bg-base-100 p-3 my-4">
            <div class="flex space-x-3 justify-evenly items-center">

                <div class="flex flex-col space-y-1 bg-base-200 w-1/4 h-16 rounded-xl justify-center items-center ">
                    <label for="" class=" font-medium text-primary w-[90%] flex justify-between items-center">
                        <span>Total leads this month</span>
                        <span class="text-lg font-semibold text-secondary">{{$lpm}}</span>
                    </label>
                    {{-- <progress class="progress progress-success w-[90%] mx-auto" value="50" max="100"></progress> --}}
                </div>

                <div class="flex flex-col space-y-1 bg-base-200 w-1/4 rounded-xl items-center py-4">
                    <label for="" class=" font-medium text-primary w-[90%] flex justify-between">
                        <span>Lead followed up this month</span>
                        <span class=" text-base font-semibold text-secondary">{{$ftm}}/{{$lpm}}</span>
                    </label>
                    @php
                        $perc_lf = ($ftm/$lpm)*100;
                    @endphp
                    <progress class="progress progress-success w-[90%] mx-auto" value="{{$perc_lf}}" max="100"></progress>

                </div>

                <div class="flex flex-col space-y-1 bg-base-200 w-1/4 rounded-xl items-center py-4">
                    <label for="" class=" font-medium text-primary w-[90%] flex justify-between">
                        <span>Leads converted this month</span>
                        <span class="text-base font-semibold text-secondary">{{$lcm}}/{{$lpm}}</span>
                    </label>
                    <progress class="progress progress-success w-[90%] mx-auto" value="{{($lcm/$lpm)*100}}" max="100"></progress>
                </div>

                <div class="flex flex-col space-y-1 bg-base-200 justify-center h-16 w-1/4 rounded-xl items-center py-4">
                    <label for="" class=" font-medium text-primary w-[90%] flex justify-between items-center">
                        <span>Total scheduled follow ups pending</span>
                        <span class="text-lg font-semibold text-secondary">{{$pf}}</span>
                    </label>

                </div>

            </div>
        </div>


      </div>

    </div>
  </div>

</x-easyadmin::app-layout>
