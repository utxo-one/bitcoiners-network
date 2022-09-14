<form action={{ route('transaction.deposit.store') }} method="POST">
    @csrf
    <input type="text" name="amount" placeholder="amount in sats">
    <button type="submit" class="btn btn-success">Deposit</button>
</form>