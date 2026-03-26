@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Nexus' || trim($slot) === 'Laravel')
<img src="https://images.vexels.com/media/users/3/137578/isolated/preview/c895a61e637f53ac91d5faf634c84794-cube-logo-geometric-polygonal.png" class="logo" alt="Nexus Logo">
@else
{!! $slot !!}
@endif

</a>
</td>
</tr>
