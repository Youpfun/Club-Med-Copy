<form action="/checkout" method="POST">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <button type="submit" id="checkout-button">Checkout</button>
</form>