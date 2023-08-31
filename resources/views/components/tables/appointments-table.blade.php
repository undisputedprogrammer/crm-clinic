@props(['appointments'])
<div class="w-[55%]">
    <div class="overflow-x-auto border border-primary rounded-xl">
        @if ($appointments != null && count($appointments)>0)

        <table class="table ">
          <!-- head -->
          <thead>
            <tr class=" text-secondary ">
              {{-- <th></th> --}}
              <th>Date</th>
              <th>Prospect name</th>
              <th>Prospect Contact No.</th>
              <th>Doctor</th>
              {{-- <th></th> --}}
            </tr>
          </thead>
          <tbody>
            @foreach ($appointments as $appointment)
                <tr
                @click.prevent.stop="
                $dispatch('dataupdate',{
                    appointment: {{json_encode($appointment)}},
                    target: 'appointment-details'
                })"
                class="text-base-content hover:bg-base-100 relative">
                    {{-- <th>{{$loop->index + 1}}</th> --}}
                    <td>{{$appointment->appointment_date}}</td>
                    <td>{{$appointment->lead->name}}</td>
                    <td>{{$appointment->lead->phone}}</td>
                    <td>{{$appointment->doctor->name ?? 'Not specified'}}</td>
                    {{-- <td>
                        <button @click.prevent.stop="$dispatch('appointmentedit', {id: {{$appointment->id}}, name: '{{$appointment->name}}', department: '{{$appointment->department}}'});" class="btn btn-ghost btn-xs text-warning" type="button">
                            <x-easyadmin::display.icon icon="easyadmin::icons.edit" height="h-4" width="w-4"/>
                        </button>
                    </td> --}}
                </tr>
            @endforeach
          </tbody>
        </table>

        @else
            <h1 class=" font-semibold text-lg text-neutral-content p-4">No appointments to show</h1>
        @endif


      </div>
    <div class="mt-1.5">
        {{ $appointments->links() }}
    </div>

</div>
