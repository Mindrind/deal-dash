@extends('frontend.layouts.user_panel')

@section('panel_content')
    <div class="aiz-titlebar mt-2 mb-4">
    <div class="row align-items-center">
      <div class="col-md-6">
          <h1 class="h3">{{ translate('My Virtual Wallet') }}</h1>
      </div>
    </div>
    </div>
    <div class="row gutters-10">
      <div class="col-md-4 mx-auto mb-3" >
          <div class="bg-grad-1 text-white rounded-lg overflow-hidden">
            <span class="size-30px rounded-circle mx-auto bg-soft-primary d-flex align-items-center justify-content-center mt-3">
                <i class="las la-dollar-sign la-2x text-white"></i>
            </span>
            <div class="px-3 pt-3 pb-3">
                <div class="h4 fw-700 text-center">{{ single_price(Auth::user()->virtual_wallet->balance) }}</div>
                <div class="opacity-50 text-center">{{ translate('Virtual Wallet Balance') }}</div>
            </div>
          </div>
      </div>
      <div class="col-md-4 mx-auto mb-3" >
        <div class="p-3 rounded mb-3 c-pointer text-center bg-white shadow-sm hov-shadow-lg has-transition" onclick="show_wallet_modal()">
            <span class="size-60px rounded-circle mx-auto bg-secondary d-flex align-items-center justify-content-center mb-3">
                <i class="las la-plus la-3x text-white"></i>
            </span>
            <div class="fs-18 text-primary">{{ translate('Recharge Virtual Wallet') }}</div>
        </div>
      </div>
    </div>
@endsection

@section('modal')

  <div class="modal fade" id="wallet_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ translate('Recharge Wallet') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
              </div>
             
                  <div class="modal-body gry-bg px-3 pt-3">
                      <div class="row">
                          <div class="col-md-4">
                              <label>{{ translate('Amount')}} <span class="text-danger">*</span></label>
                          </div>
                          <div class="col-md-8">
                              <input type="number" lang="en" class="form-control mb-3" name="amount" id="amount" placeholder="{{ translate('Amount')}}" required>
                              <div class="virtual-wallet-response"></div>
                          </div>
                      </div>
                      <!-- <div class="row">
                          <div class="col-md-4">
                              <label>{{ translate('Payment Method')}} <span class="text-danger">*</span></label>
                          </div>
                          <div class="col-md-8">
                              <div class="mb-3">
                                  <select class="form-control selectpicker" data-minimum-results-for-search="Infinity" name="payment_option" data-live-search="true">
                                    @if (get_setting('paypal_payment') == 1) 
                                        <option value="paypal" selected="selected">{{ translate('Paypal')}}</option>
                                    @endif
                                    @if (get_setting('stripe_payment') == 1)
                                        <option value="stripe">{{ translate('Stripe')}}</option>
                                    @endif
                                    @if (get_setting('mercadopago_payment') == 1)
                                        <option value="mercadopago">{{ translate('Mercadopago')}}</option>
                                    @endif
                                    @if(get_setting('toyyibpay_payment') == 1)
                                        <option value="toyyibpay">{{ translate('ToyyibPay')}}</option>
                                    @endif
                                    @if (get_setting('sslcommerz_payment') == 1)
                                        <option value="sslcommerz">{{ translate('SSLCommerz')}}</option>
                                    @endif
                                    @if (get_setting('instamojo_payment') == 1)
                                        <option value="instamojo">{{ translate('Instamojo')}}</option>
                                    @endif
                                    @if (get_setting('paystack') == 1)
                                        <option value="paystack">{{ translate('Paystack')}}</option>
                                    @endif
                                    @if (get_setting('voguepay') == 1)
                                        <option value="voguepay">{{ translate('VoguePay')}}</option>
                                    @endif
                                    @if (get_setting('payhere') == 1)
                                        <option value="payhere">{{ translate('Payhere')}}</option>
                                    @endif
                                    @if (get_setting('ngenius') == 1)
                                        <option value="ngenius">{{ translate('Ngenius')}}</option>
                                    @endif
                                    @if (get_setting('razorpay') == 1)
                                        <option value="razorpay">{{ translate('Razorpay')}}</option>
                                    @endif
                                    @if (get_setting('iyzico') == 1)
                                        <option value="iyzico">{{ translate('Iyzico')}}</option>
                                    @endif
                                    @if (get_setting('bkash') == 1)
                                        <option value="bkash">{{ translate('Bkash')}}</option>
                                    @endif
                                    @if (get_setting('nagad') == 1)
                                        <option value="nagad">{{ translate('Nagad')}}</option>
                                    @endif
                                    @if (get_setting('payku') == 1)
                                        <option value="payku">{{ translate('Payku')}}</option>
                                    @endif
                                    @if(addon_is_activated('african_pg'))
                                        @if (get_setting('mpesa') == 1)
                                            <option value="mpesa">{{ translate('Mpesa')}}</option>
                                        @endif
                                        @if (get_setting('flutterwave') == 1)
                                            <option value="flutterwave">{{ translate('Flutterwave')}}</option>
                                        @endif
                                        @if (get_setting('payfast') == 1)
                                            <option value="payfast">{{ translate('PayFast')}}</option>
                                        @endif
                                    @endif
                                    @if (addon_is_activated('paytm') && get_setting('paytm_payment'))
                                        <option value="paytm">{{ translate('Paytm')}}</option>
                                    @endif
                                    @if(get_setting('authorizenet') == 1)
                                        <option value="authorizenet">{{ translate('Authorize Net')}}</option>
                                    @endif
                                    @if (addon_is_activated('paytm') && get_setting('myfatoorah') == 1)
											<option value="myfatoorah">{{translate('MyFatoorah')}}</option>
									@endif
                                  </select>
                              </div>
                          </div>
                      </div> -->
                      <div class="form-group text-right">
                          <button type="button" class="btn btn-sm btn-primary transition-3d-hover mr-1" id="virtual_wallet">{{translate('Confirm')}}</button>
                      </div>
                  </div>
          </div>
      </div>
  </div>


  <!-- offline payment Modal -->
  <div class="modal fade" id="offline_wallet_recharge_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">{{ translate('Offline Recharge Wallet') }}</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
              </div>
              <div id="offline_wallet_recharge_modal_body"></div>
          </div>
      </div>
  </div>

@endsection

@section('script')
    <script type="text/javascript">
        function show_wallet_modal(){
            $('#wallet_modal').modal('show');
        }

        function show_make_wallet_recharge_modal(){
            $.post('{{ route('virtual_wallet.deposit') }}', {_token:'{{ csrf_token() }}'}, function(data){
                $('#offline_wallet_recharge_modal_body').html(data);
                $('#offline_wallet_recharge_modal').modal('show');
            });
        }


        $('#amount').on('input', function() {
            $(".virtual-wallet-response").html("");
            $(".virtual-wallet-response").removeClass("alert alert-danger mb-4");
            return false;
        });






        $("#virtual_wallet").click(function(){


            let amount = $("#amount").val();

            if(!Number(amount) || amount <= 0){
                $(".virtual-wallet-response").html("Invalid amount");
                $(".virtual-wallet-response").addClass("alert alert-danger mb-4");
                return false;
            }



           $.ajax({
            url: "/virtual-wallet/deposit",
            type: 'POST',
            data: {
            amount: amount,
            currency: 'USD'
            },
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
            },
            success: function(response) {
                $("#amount").val("");
                $('#wallet_modal').modal('hide');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $(".virtual-wallet-response").html(errorThrown);
                $(".virtual-wallet-response").addClass("alert alert-danger mb-4");
                return false;
            }
           })
        });
       


    </script>
@endsection