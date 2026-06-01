{{-- Data Table Component --}}
<div class="table-responsive">
    <table class="table table-sm" style="font-size: 13px;">
        <thead style="background: #f9fafb; border-bottom: 0.5px solid #e5e7eb;">
            <tr>
                @foreach($headers as $header)
                    <th style="padding: 10px 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px;">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody style="border-bottom: 0.5px solid #e5e7eb;">
            {{ $slot }}
        </tbody>
    </table>
</div>
