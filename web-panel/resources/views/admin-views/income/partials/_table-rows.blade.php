@forelse ($incomes as $key => $income)
    <tr>
        <td data-column="date">{{ $income->date }}</td>
        <td data-column="account">
            @if($income->account)
                {{$income->account->account}}
            @else
                <span class="badge badge-danger">{{ \App\CPU\translate('Account Deleted') }}</span>
            @endif
        </td>
        <td data-column="type">
            <span class="badge badge-info">
                {{ $income->tran_type}} <br>
            </span>
        </td>
        <td data-column="amount">
            {{ $income->amount ." ".\App\CPU\Helpers::currency_symbol()}}
        </td>
        <td data-column="description">
            {{ Str::limit($income->description,30) ?? 'N/A' }}
        </td>
        <td data-column="debit">
            @if ($income->debit)
                {{ $income->amount ." ".\App\CPU\Helpers::currency_symbol()}}
            @else
                {{ 0 ." ".\App\CPU\Helpers::currency_symbol()}}
            @endif
        </td>
        <td data-column="credit">
            @if ($income->credit)
                {{ $income->amount ." ".\App\CPU\Helpers::currency_symbol()}}
            @else
                {{ 0 ." ".\App\CPU\Helpers::currency_symbol()}}
            @endif
        </td>
        <td data-column="balance">
            {{ $income->balance ." ".\App\CPU\Helpers::currency_symbol()}}
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
