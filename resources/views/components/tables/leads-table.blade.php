@props(['leads'])

{{-- {{dd($leads[0])}} --}}

<div class=" w-[96%] mx-auto md:w-[55%] overflow-x-scroll hide-scroll">
    <div class="overflow-x-auto border border-primary rounded-xl">
        @if ($leads != null && count($leads) > 0)

            <table class="table ">
                <!-- head -->
                <thead>
                    <tr class=" text-secondary ">
                        <th><input type="checkbox" class=" checkbox checkbox-secondary"></th>
                        <th></th>
                        <th>Name</th>
                        <th>City</th>
                        <th>Phone</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($leads as $lead)
                        <tr x-data="{ questions: null }" class="text-base-content hover:bg-base-100"
                            :class=" name == `{{ $lead->name }}` ? 'bg-base-100 font-medium' : ''"
                            @click="
                                $dispatch('detailsupdate',{lead : {{ json_encode($lead) }}, remarks: {{ json_encode($lead->remarks) }}, id: {{ $lead->id }}, followups: {{ $lead->followups }}, qnas: {{ json_encode($lead->qnas) }}})">

                            <th><input type="checkbox" :checked="selectedLeads[{{$lead->id}}] != null ? true : false " @click="selectLead($el,{{$lead}})" class="checkbox checkbox-secondary checkbox-sm"></th>

                            <th>{{ $lead->id }}</th>
                            <td>{{ $lead->name }}</td>
                            <td>{{ $lead->city }}</td>
                            <td>{{ $lead->phone }}</td>

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
