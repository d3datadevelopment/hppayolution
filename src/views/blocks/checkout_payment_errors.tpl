[{d3modcfgcheck modid="d3heidelpay"}]
[{/d3modcfgcheck}]

[{if $mod_d3heidelpay && $showD3PayolutionError}]
    <div class="alert alert-danger">[{$showD3PayolutionErrorText}]</div>
[{else}]
    [{$smarty.block.parent}]
[{/if}]
