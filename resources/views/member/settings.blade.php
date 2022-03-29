{{--
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2022 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

@extends('layouts.layout_home')

@section('title')
    {{ __('app.settings') }}
@endsection

@section('content')
    <div class="column is-2"></div>

    <div class="column is-8 fixed-form is-margin-top-20">
        <div>
            <h2>{{ __('app.settings') }}</h2>
        </div>
        <hr/>

        <div class="tabs">
            <ul>
                <li id="tabProfile" class="is-active"><a href="javascript:void(0);" onclick="window.vue.showTabMenu('tabProfile');">{{ __('app.profile') }}</a></li>
                <li id="tabSecurity"><a href="javascript:void(0);" onclick="window.vue.showTabMenu('tabSecurity');">{{ __('app.security') }}</a></li>
                <li id="tabNotifications"><a href="javascript:void(0);" onclick="window.vue.showTabMenu('tabNotifications');">{{ __('app.notifications') }}</a></li>
                <li id="tabMembership"><a href="javascript:void(0);" onclick="window.vue.showTabMenu('tabMembership');">{{ __('app.membership') }}</a></li>
            </ul>
        </div>

        <div id="tabProfile-form">
            <div>
                <span><img class="is-rounded-image" src="{{ asset('gfx/avatars/' . $self->avatar) }}" alt="avatar"></span>
                <span><a href="{{ url('/user/' . $self->slug) }}">{{ __('app.view_profile') }}</a></span>
            </div>

            <br/>

            <form method="POST" action="{{ url('/settings') }}" enctype="multipart/form-data">
                @csrf

                <div class="field">
                    <label class="label">{{ __('app.name') }}</label>
                    <div class="control">
                        <input type="text" name="name" value="{{ $self->name }}">
                    </div>
                </div>

                <div class="field">
                    <label class="label">{{ __('app.birthday') }}</label>
                    <div class="control">
                        <input type="date" class="input" name="birthday" value="{{ date('Y-m-d', strtotime($self->birthday)) }}">
                    </div>
                </div>

                <div class="field">
                    <label class="label">{{ __('app.gender') }}</label>
                    <div class="control">
                        <select name="gender">
                            <option value="0" @if ($self->gender === 0) {{ 'selected' }} @endif>{{ __('app.gender_unspecified') }}</option>
                            <option value="1" @if ($self->gender === 1) {{ 'selected' }} @endif>{{ __('app.gender_male') }}</option>
                            <option value="2" @if ($self->gender === 2) {{ 'selected' }} @endif>{{ __('app.gender_female') }}</option>
                            <option value="3" @if ($self->gender === 3) {{ 'selected' }} @endif>{{ __('app.gender_diverse') }}</option>
                        </select>
                    </div>
                </div>

                <div class="field">
                    <label class="label">{{ __('app.location') }}</label>
                    <div class="control">
						<select name="location">
							@foreach (\App\LocationModel::fetch() as $location)
								<option value="{{ $location->name }}" @if ($location->name === $self->location) {{ 'selected' }} @endif>{{ ucfirst($location->name) }}</option>
							@endforeach
						</select>
                    </div>
                </div>

                <div class="field">
                    <label class="label">{{ __('app.bio') }}</label>
                    <div class="control">
                        <textarea name="bio">{{ $self->bio }}</textarea>
                    </div>
                </div>

                <div class="field">
                    <label class="label">{{ __('app.avatar') }}</label>
                    <div class="control">
                        <input type="file" data-role="file" data-type="2" name="avatar">
                    </div>
                </div>

                <br/>

                <input type="submit" class="button is-success" value="{{ __('app.save') }}">
            </form>
        </div>

        <div id="tabSecurity-form" class="is-hidden">
            <form method="POST" action="{{ url('/settings/password') }}">
                @csrf

                <div class="field">
                    <label class="label">{{ __('app.password') }}</label>
                    <div class="control">
                        <input type="password" name="password">
                    </div>
                </div>

                <div class="field">
                    <label class="label">{{ __('app.password_confirmation') }}</label>
                    <div class="control">
                        <input type="password" name="password_confirmation">
                    </div>
                </div>

                <input type="submit" class="button is-success" value="{{ __('app.save') }}">
            </form>

            <hr/>

            <form method="POST" action="{{ url('/settings/email') }}">
                @csrf

                <div class="field">
                    <label class="label">{{ __('app.email') }}</label>
                    <div class="control">
                        <input type="email" name="email" value="{{ $self->email }}">
                    </div>
                </div>

                <input type="submit" class="button is-success" value="{{ __('app.save') }}">
            </form>

            <hr/>

            <div>
                <input type="checkbox" data-role="checkbox" data-style="2" data-caption="{{ __('app.public_profile_label') }}" value="1" onclick="window.vue.togglePublicProfile(this);" @if ($self->public_profile) {{ 'checked' }} @endif>
                <br/><br/>
            </div>
        </div>

        <div id="tabNotifications-form" class="is-hidden">
            <form method="POST" action="{{ url('/settings/notifications') }}">
                @csrf

                <div class="field">
                    <input type="checkbox" name="newsletter" data-role="checkbox" data-style="2" value="1" data-caption="{{ __('app.newsletter_notice') }}" @if ($self->newsletter) {{ 'checked' }} @endif>
                </div>

                <div class="field">
                    <input type="checkbox" name="email_on_message" data-role="checkbox" data-style="2" value="1" data-caption="{{ __('app.email_on_message_notice') }}" @if ($self->email_on_message) {{ 'checked' }} @endif>
                </div>

                <div class="field">
                    <input type="checkbox" name="email_on_comment" data-role="checkbox" data-style="2" value="1" data-caption="{{ __('app.email_on_comment_notice') }}" @if ($self->email_on_comment) {{ 'checked' }} @endif>
                </div>

                <div class="field">
                    <input type="checkbox" name="email_on_participated" data-role="checkbox" data-style="2" value="1" data-caption="{{ __('app.email_on_participated_notice') }}" @if ($self->email_on_participated) {{ 'checked' }} @endif>
                </div>

                <div class="field">
                    <input type="checkbox" name="email_on_fav_created" data-role="checkbox" data-style="2" value="1" data-caption="{{ __('app.email_on_fav_created_notice') }}" @if ($self->email_on_fav_created) {{ 'checked' }} @endif>
                </div>

                <div class="field">
                    <input type="checkbox" name="email_on_act_canceled" data-role="checkbox" data-style="2" value="1" data-caption="{{ __('app.email_on_act_canceled_notice') }}" @if ($self->email_on_act_canceled) {{ 'checked' }} @endif>
                </div>

                <input type="submit" class="button is-success" value="{{ __('app.save') }}">
            </form>
        </div>

        <div id="tabMembership-form" class="is-hidden">
            @if ((env('STRIPE_ENABLE')) && (!$self->pro))
                <div>
                    <strong><a href="javascript:void(0);" onclick="window.vue.bShowPurchaseProMode = true;">{{ __('app.purchase_pro_mode') }}</a></strong>
                </div>

                <div>
                    <hr/>
                </div>
            @endif

            @if (env('APP_ACCOUNTVERIFICATION'))
            <div>
                @if ($self->state === \App\VerifyModel::STATE_INPROGRESS)
                    <strong>{{ __('app.verification_in_progress') }}</strong>
                @elseif ($self->state === \App\VerifyModel::STATE_VERIFIED)
                    <strong>{{ __('app.verification_succeeded') }}</strong>
                @else
                    <form method="POST" action="{{ url('/settings/verify') }}" id="frmVerify" enctype="multipart/form-data">
                        @csrf

                        <div class="field">
                            <label class="label">
                                {{ __('app.verify_account') }}
                            </label>
                        </div>

                        <div class="field">
                            <label class="label">{{ __('app.identity_card_front') }}</label>
                            <div class="control">
                                <input type="file" name="idcard_front" data-role="file" data-type="2">
                            </div>
                        </div>

                        <div class="field">
                            <label class="label">{{ __('app.identity_card_back') }}</label>
                            <div class="control">
                                <input type="file" name="idcard_back" data-role="file" data-type="2">
                            </div>
                        </div>

                        <div class="field">
                            <div class="control">
                                <input type="checkbox" name="confirmation" data-role="checkbox" data-type="2" data-caption="{{ __('app.confirm_verify_permission') }}" value="1" onclick="if (this.checked) { document.getElementById('btnVerify').disabled = false; } else { document.getElementById('btnVerify').disabled = true; }">
                            </div>
                        </div>

                        <div class="field">
                            <div class="control">
                                <input type="button" class="button is-link" id="btnVerify" value="{{ __('app.submit') }}" onclick="if (!this.disabled) { document.getElementById('frmVerify').submit(); }" disabled>
                            </div>
                        </div>
                    </form>
                @endif
            </div>

            <div>
                <hr/>
            </div>
            @endif

            <div class="is-margin-top-20">
                <form method="POST" action="{{ url('/settings/delete') }}">
                    @csrf

                    <div class="field">
                        <label class="label">
                            {{ __('app.delete_account_notice') }}
                        </label>
                    </div>

                    <div class="field">
                        <label class="label">{{ $captchadata[0] }} + {{ $captchadata[1] }} = ?</label>
                        <div class="control">
                            <input type="text" name="captcha">
                        </div>
                    </div>

                    <input type="submit" class="button is-success" value="{{ __('app.delete') }}">
                </form>
            </div>
        </div>

        @if (env('STRIPE_ENABLE') == true)
            <div class="modal" :class="{'is-active': bShowPurchaseProMode}">
                <div class="modal-background"></div>
                <div class="modal-card">
                    <header class="modal-card-head is-stretched">
                        <p class="modal-card-title">{{ __('app.purchase_pro_mode_title') }}</p>
                        <button class="delete" aria-label="close" onclick="vue.bShowPurchaseProMode = false;"></button>
                    </header>
                    <section class="modal-card-body is-stretched">
                        <div class="field">
                            {!! __('app.purchase_pro_mode_info', ['costs' => env('STRIPE_COSTS_LABEL')]) !!}
                        </div>

                        <form action="{{ url('/payment/charge') }}" method="post" id="payment-form" class="stripe">
                            @csrf

                            <div class="form-row">
                                <label for="card-element">
                                    {{ __('app.credit_or_debit_card') }}
                                </label>
                                <div id="card-element"></div>

                                <div id="card-errors" role="alert"></div>
                            </div>

                            <br/>

                            <button class="button is-link">{{ __('app.submit_payment') }}</button>
                        </form>
                    </section>
                    <footer class="modal-card-foot is-stretched">
                        <button class="button" onclick="vue.bShowPurchaseProMode = false;">{{ __('app.close') }}</button>
                    </footer>
                </div>
            </div>
			@endif
    </div>

    <div class="column is-2"></div>
@endsection

@section('javascript')
    <script>
        @if (env('STRIPE_ENABLE'))
                const stripeTokenHandler = (token) => {
                    const form = document.getElementById('payment-form');
                    const hiddenInput = document.createElement('input');
                    hiddenInput.setAttribute('type', 'hidden');
                    hiddenInput.setAttribute('name', 'stripeToken');
                    hiddenInput.setAttribute('value', token.id);
                    form.appendChild(hiddenInput);
                    form.submit();
                }

				var stripe = Stripe('{{ env('STRIPE_TOKEN_PUBLIC') }}');
				var elements = stripe.elements();

				const style = {
					base: {
						fontSize: '16px',
						color: '#32325d',
					},
				};

				const card = elements.create('card', {style});
				card.mount('#card-element');

				const form = document.getElementById('payment-form');
				form.addEventListener('submit', async (event) => {
					event.preventDefault();

					const {token, error} = await stripe.createToken(card);

					if (error) {
						const errorElement = document.getElementById('card-errors');
						errorElement.textContent = error.message;
					} else {
						stripeTokenHandler(token);
					}
				});
			@endif
    </script>
@endsection