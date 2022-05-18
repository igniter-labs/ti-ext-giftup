@empty($giftUpOptions->companyId)
    <p
        class="alert alert-warning"
    >Notice to site admin: Please connect your Gift Up! account to TastyIgniter in Settings > Gift Up!</p>
@else
    <div
        class="gift-up-target"
        data-site-id="{{ $giftUpOptions->companyId }}"
        data-domain="{{ $giftUpOptions->domain }}"
        data-product-id="{{ $giftUpOptions->product }}"
        data-group-id="{{ $giftUpOptions->group }}"
        data-language="{{ $giftUpOptions->language }}"
        data-purchaser-name="{{ $giftUpOptions->purchaserName }}"
        data-purchaser-email="{{ $giftUpOptions->purchaserEmail }}"
        data-recipient-name="{{ $giftUpOptions->recipientName }}"
        data-recipient-email="{{ $giftUpOptions->recipientEmail }}"
        data-step="{{ $giftUpOptions->step }}"
        data-who-for="{{ $giftUpOptions->whoFor }}"
        data-promo-code="{{ $giftUpOptions->promoCode }}"
        data-hide-artwork="{{ $giftUpOptions->hideArtwork }}"
        data-hide-groups="{{ $giftUpOptions->hideGroups }}"
        data-hide-ungrouped-items="{{ $giftUpOptions->hideUngroupedItems }}"
        data-hide-custom-value="{{ $giftUpOptions->hideCustomValue }}"
        data-custom-value-amount="{{ $giftUpOptions->customValueAmount }}"
        data-platform="TastyIgniter"
    ></div>
    <script type="text/javascript">
        (function (g, i, f, t, u, p, s) {
            g[u] = g[u] || function () {
                (g[u].q = g[u].q || []).push(arguments)
            };
            p = i.createElement(f);
            p.async = 1;
            p.src = t;
            s = i.getElementsByTagName(f)[0];
            s.parentNode.insertBefore(p, s);
        })(window, document, "script", "https://cdn.giftup.app/dist/gift-up.js", "giftup");
    </script>
@endempty
