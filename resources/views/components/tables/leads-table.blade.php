@props(['leads'])

{{-- {{dd($leads[0])}} --}}
<div class="w-[55%]">
    <div class="overflow-x-auto border border-primary rounded-xl">
        @if ($leads != null && count($leads)>0)






        <table class="table ">
          <!-- head -->
          <thead>
            <tr class=" text-secondary ">
              <th></th>
              <th>Name</th>
              <th>City</th>
              <th>Phone</th>
            </tr>
          </thead>
          <tbody>



            @foreach ($leads as $lead)



                <tr x-data="{questions : null}"  class="text-base-content hover:bg-base-100" :class=" name == `{{$lead->name}}` ? 'bg-base-100 font-medium' : '' "
                    @click.prevent.stop="
                    console.log({{$lead->remarks}});
                    $dispatch('detailsupdate',{lead : {{json_encode($lead)}}, remarks: {{json_encode($lead->remarks)}}, id: {{$lead->id}}, followups: {{$lead->followups}}, answers: {{json_encode($lead->answers)}}})"




                    >
                    <th>{{$lead->id}}</th>
                    <td>{{$lead->name}}</td>
                    <td>{{$lead->city}}</td>
                    <td>{{$lead->phone}}</td>
                </tr>







            @endforeach




          </tbody>
        </table>

        @else
            <h1 class=" font-semibold text-lg text-neutral-content p-4">No leads to show</h1>
        @endif


      </div>
    <div class="mt-1.5">
        {{ $leads->links() }}
    </div>




</div>
