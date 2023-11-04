<div class=" mt-2.5">
    <p class="text-base font-medium text-secondary">Follow up history</p>

    {{-- loading --}}
    <div x-cloak x-show="historyLoading" class=" w-full flex justify-center">
        <span class="loading loading-bars loading-xs text-center my-4 text-primary"></span>
    </div>

    {{-- looping through history --}}
    <template x-show="!historyLoading" x-for="item in fphistory" >
        <div x-data="{agent: item.user}" x-show="item.actual_date != null" class=" mt-2 mr-1 bg-neutral p-2 rounded-lg">
            <p class=" font-medium">Date : <span class=" text-primary" x-text="formatDate(item.actual_date)"></span></p>

            {{-- <template x-if=""> --}}
                <p  class=" font-medium">Agent : <span class=" text-primary" x-text="agent != null ? agent.name : '' "></span></p>
            {{-- </template> --}}

                <p  class=" font-medium">Call Status :
                    <span class=" text-primary" x-text="item.call_status != null ? item.call_status : '' " :class="item.call_status == 'Responsive' ? ' text-success' : ' text-error' "></span>
                </p>

                <p class="font-medium">Follow up remarks</p>

                <ul>
                <template x-if="item.remarks != undefined">
                    <template x-for="remark in item.remarks">
                        <li x-text="remark.remark"></li>
                    </template>
                </template>
            </ul>
        </div>
    </template>

    <p x-show="!historyLoading" class=" text-error" x-text=" history.length == 1 && fp.actual_date == null ? 'No follow ups completed yet' : '' "></p>


</div>
