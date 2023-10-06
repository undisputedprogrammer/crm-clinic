@props(['status'])
<form action="" class=" flex space-x-1 items-center" @submit.prevent.stop="filterByStatus($el)" id="filter-by-status-form">
    <select name="status" id="select-status" class=" select text-base-content select-sm text-xs focus:ring-0 focus:outline-none">
        <option value="none" :selected="'{{$status}}'=='null' || '{{$status}}'=='none'">Fresh Leads</option>
        <option value="all" :selected="'{{$status}}'=='all' ">All leads</option>
        @foreach (config('appSettings')['lead_statuses'] as $st)
        <template x-if="'{{$st}}' != 'Created'">
            <option value="{{$st}}" :selected="'{{$status}}' == '{{$st}}' ">{{$st}}</option>
        </template>
        @endforeach
    </select>
    <button type="submit" class=" btn btn-sm btn-primary">Filter</button>
</form>
