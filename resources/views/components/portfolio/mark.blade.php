@props(['small' => false])

{{-- Brand monogram, used in the nav and the footer. --}}
<span @class([
    'grid place-items-center rounded-[11px] bg-[linear-gradient(140deg,#2ee6a6,#12b47f)] font-bold text-on-accent',
    'h-[34px] w-[34px] text-[15px]' => ! $small,
    'h-[30px] w-[30px] rounded-[9px] text-[13px]' => $small,
])>MG</span>
