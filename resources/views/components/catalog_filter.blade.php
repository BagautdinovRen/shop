@foreach($properties as $propertyId => $property)
    <div class="section-property">
        <div class="section-property__name">
            <span>{{ $property['name'] }}</span>
        </div>
        <div class="section-property__values">
            @foreach($property['values'] as $valueId => $value)
                <label>
                    <input type="checkbox" name="properties[{{ $propertyId }}]" value="{{ $valueId }}" data-property="{{ $propertyId }}"
                           @if(!empty($checked[$propertyId]) && (array_search($valueId, $checked[$propertyId]) !== false)) checked @endif>
                    <span>{{ $value['value'] }}</span>
                    @if(!empty($value['count']))
                        <span class="ml-2">({{ $value['count'] }})</span>
                    @endif
                </label>
            @endforeach
        </div>
    </div>
@endforeach

@push('js')
    <script>
        $(document).ready(function() {
            $(document).on('click', '.section-property__values input[type=checkbox]', function(){
                let data = {section: {{ $section->id }}, _token: "{{ csrf_token() }}"},
                    arrData = $(this).closest('form').find('input[type=checkbox]:checked'),
                    queryString = '';

                arrData.each(function(index){
                    let item = $(this);

                    if ((typeof data[item.attr('name')] !== 'undefined') && data[item.attr('name')].length){
                        data[item.attr('name')].push(item.val());
                        queryString = queryString + ',' + item.val();
                    }
                    else
                    {
                        if (index === 0)
                            queryString += '?';
                        else
                            queryString += '&';

                        data[item.attr('name')] = [item.val()];
                        queryString = queryString + 'p_' + item.data('property') + '=' + item.val();
                    }
                });

                let url = '{{ route('front.catalog.filter', $section) }}/' + queryString;

                history.pushState('', '', url);

                if (arrData.length)
                    url += '&ajax=Y';
                else
                    url += '?ajax=Y';

                $.ajax({
                    type: "GET",
                    url: url,
                    //data: data,
                    success: function(result){
                        $('.section-content').html(result);
                    },
                });
            });
        });
    </script>
@endpush
