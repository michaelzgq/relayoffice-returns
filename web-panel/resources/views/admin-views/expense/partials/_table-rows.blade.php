@forelse ($expenses as $key => $expense)
    <tr>
        <td data-column="date">{{ $expense->date }}</td>
        <td data-column="account">
            @if($expense->account)
                {{$expense->account->account}}
            @else
                <span class="badge badge-danger">{{ \App\CPU\translate('Account Deleted') }}</span>
            @endif
        </td>
        <td data-column="type">
                                            <span class="badge badge-danger">
                                                {{ $expense->tran_type }}
                                            </span>
        </td>
        <td data-column="amount">
            {{ $expense->amount . ' ' . \App\CPU\Helpers::currency_symbol() }}
        </td>
        <td data-column="description">
            {{ Str::limit($expense->description, 30) ?? 'N/A' }}
        </td>
        <td data-column="debit">
            @if ($expense->debit)
                {{ $expense->amount . ' ' . \App\CPU\Helpers::currency_symbol() }}
            @else
                {{ 0 . ' ' . \App\CPU\Helpers::currency_symbol() }}
            @endif
        </td>
        <td data-column="credit">
            @if ($expense->credit)
                {{ $expense->amount . ' ' . \App\CPU\Helpers::currency_symbol() }}
            @else
                {{ 0 . ' ' . \App\CPU\Helpers::currency_symbol() }}
            @endif
        </td>
        <td data-column="balance">
            {{ $expense->balance . ' ' . \App\CPU\Helpers::currency_symbol() }}
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="text-center p-4">
            <img class="mb-3 img-one-ex"
                 src="{{ asset('assets/admin/svg/illustrations/sorry.svg') }}"
                 alt="{{ \App\CPU\translate('Image Description') }}">
            <p class="mb-0">{{ \App\CPU\translate('No_data_to_show') }}</p>
        </td>
    </tr>
@endforelse
