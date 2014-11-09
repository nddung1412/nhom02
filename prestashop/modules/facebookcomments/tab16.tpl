<section class="page-product-box">
    <h3 class="page-product-heading">{l s='Comments' mod='facebookcomments'} (<fb:comments-count href="http://{$smarty.server.HTTP_HOST}{$smarty.server.REQUEST_URI}"></fb:comments-count>)</h3>
    {assign var=fcbc_width value=$var['fcbc_width']}
    {assign var=fcbc_nbp value=$var['fcbc_nbp']}
    {assign var=fcbc_scheme value=$var['fcbc_scheme']}
    {literal}
    <style>
    .fb_ltr, .fb_iframe_widget, .fb_iframe_widget span {width: 100%!important}
    </style>
    {/literal}
    <div id="fcbc" class="">
    <fb:comments href="http://{$smarty.server.HTTP_HOST}{$smarty.server.REQUEST_URI}" colorscheme="{$fcbc_scheme}"  width="{$fcbc_width}"></fb:comments>
    </div>
</section>