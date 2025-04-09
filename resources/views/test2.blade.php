

<x-layout Title="Test2">
    <div class="col content">

    <table>
        <th>id</th>
        <th>uprn</th>
        <th>house_number</th>
        <th>house_name</th>
        <th>street</th>
        <th>town</th>
        <th>parish</th>
        <th>county</th>
        <th>postcode</th>
        @foreach((new \App\Http\Controllers\MachineLearningController)->getData() as $d)
            <tr>
                <td>
                    {{ $d[0] }}
                </td>
                <td>
                    {{ $d[1] }}
                </td>
                <td>
                    {{ $d[2] }}
                </td>
                <td>
                    {{ $d[3] }}
                </td>
                <td>
                    {{ $d[4] }}
                </td>
                <td>
                    {{ $d[5] }}
                </td>
                <td>
                    {{ $d[6] }}
                </td>
                <td>
                    {{ $d[7] }}
                </td>
                <td>
                    {{ $d[8] }}
                </td>
            </tr>
        @endforeach
    </table>



    </div>
</x-layout>
