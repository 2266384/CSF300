@inject('attributesservice', 'App\Services\AttributeService')


<ul>
    @foreach($attributesservice->attributesWithActions() as $att)
    <li>
        {{ $att['index'] }}
        {{ $att['code'] }}
        {{ $att['action'] }}
        {{ $att['targetcode'] }}
    </li>
    @endforeach
</ul>
