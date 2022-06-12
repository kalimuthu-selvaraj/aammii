<table border="1" style=" border-collapse: collapse;">
    <thead>
        <tr>
			<th width="150px" height="30px" align="center" valign="center" style="background-color:orange;font-weight:bold;border:1em solid black">S.NO</th>
            @foreach ($columns as $key => $value)
				@if($value != 'PRODUCT_ID')
					<th width="150px" height="30px" align="center" valign="center" style="background-color:orange;font-weight:bold;border:1em solid black">{{ $value == 'increment_id' ? 'order_id' : $value }}</th>
				@endif
			@endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr >
				<td height="30px"  align="center" valign="center"  style="border:1em solid black"> {{ $loop->iteration }}</td>
                @foreach($record as $column => $value)
                    <td height="30px"  align="center" valign="center"  style="border:1em solid black">{{ isset($value) ? $value : "\n" }} </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>