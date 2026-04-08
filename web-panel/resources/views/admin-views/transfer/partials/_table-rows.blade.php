@forelse ($transfers as $key => $transfer)
    <tr>
        <td data-column="date">{{ $transfer->date }}</td>
        <td data-column="account">
            @if($transfer->account)
                {{$transfer->account->account}}
            @else
                <span class="badge badge-danger">{{ \App\CPU\translate('Account Deleted') }}</span>
            @endif
        </td>
        <td data-column="type">
                                            <span class="badge badge-warning">
                                                {{ $transfer->tran_type }} <br>
                                            </span>
        </td>
        <td data-column="amount">
            {{ $transfer->amount . ' ' . \App\CPU\Helpers::currency_symbol() }}
        </td>
        <td data-column="description">
            {{ Str::limit($transfer->description, 30) }}
        </td>
        <td data-column="debit">
            @if ($transfer->debit)
                {{ $transfer->amount . ' ' . \App\CPU\Helpers::currency_symbol() }}
            @else
                {{ 0 . ' ' . \App\CPU\Helpers::currency_symbol() }}
            @endif
        </td>
        <td data-column="credit">
            @if ($transfer->credit)
                {{ $transfer->amount . ' ' . \App\CPU\Helpers::currency_symbol() }}
            @else
                {{ 0 . ' ' . \App\CPU\Helpers::currency_symbol() }}
            @endif
        </td>
        <td data-column="balance">
            {{ $transfer->balance . ' ' . \App\CPU\Helpers::currency_symbol() }}
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="text-center p-4">
            <img class="mb-3 img-one-in" src="{{asset('assets/admin/svg/illustrations/sorry.svg')}}" alt="{{\App\CPU\translate('Image Description')}}">
            <p class="mb-0">{{ \App\CPU\translate('No_data_to_show')}}</p>
        </td>
    </tr>
@endforelse
