@props(['route','centers'])
<form action="" class=" flex space-x-1 items-center" @submit.prevent.stop="
selected = !selected;
filterByCenter($el,'{{route($route)}}');" id="filter-by-center-form">
    <select name="center" id="select-center" class=" select text-base-content select-sm text-xs focus:ring-0 focus:outline-none">
        <option value="all">All Centers</option>
        @foreach ($centers as $center)
            <option value="{{$center->id}}" :selected="selectedCenter == '{{$center->id}}' ">{{$center->name}}</option>
        @endforeach
    </select>
    <button type="submit" class=" btn btn-sm btn-primary">Filter</button>
</form>
