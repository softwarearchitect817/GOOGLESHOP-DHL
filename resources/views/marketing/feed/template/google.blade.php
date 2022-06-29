<?php echo '<?xml version="1.0"?>'; ?>
<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
    @foreach($categories as $category)
        <channel>
        <title>{{'<![CDATA[ '.$category->name.' ]]>'}}</title>
        <link>{{'<![CDATA[ '.$domain->full_domain.' ]]>'}}</link>
        @foreach($category->take_20_product as $row)
        @php 
        $content = json_decode($row->content->value);
        $preview = json_decode($row->preview);
        $brands = $row->brands;
        $currency = currency_info();
        $currency = $currency['currency_default']->currency_name;
        @endphp
        <item>
        <g:id>{{'<![CDATA[ '.$row->id.' ]]>'}}</g:id>
        <g:title>{{'<![CDATA[ '.$row->title.' ]]>'}}</g:title>
        <g:description>{{'<![CDATA[ '.strip_tags($content->content).' ]]>'}}</g:description>
        <g:link>{{'<![CDATA[ '.$domain->full_domain.'/product/'.$row->slug.'/'.$row->id.' ]]>'}}</g:link>
        <g:image_link>@if(isset($preview->media)){{'<![CDATA['.'https:'.$preview->media->url.' ]]>'}}@endif</g:image_link>
        <g:brand>@if(count($brands) > 0){{'<![CDATA['.$brands.' ]]>'}} @endif</g:brand>
        <g:condition>new</g:condition>
        <g:availability>@if($row->featured){{'<![CDATA[ in stock ]]>' }}@else {{'<![CDATA[ out of stock ]]>'}} @endif</g:availability>
        <g:price>{{'<![CDATA['.$currency.' '.$row->price->price.']]>'}}</g:price>
        <g:shipping>
        <g:country>{{'<![CDATA[ '.'UK'.' ]]>'}}</g:country>
        <g:service>{{'<![CDATA[ '.'Standard'.' ]]>'}}</g:service>
        <g:price>{{'<![CDATA[ '.'4.95 GBP'.' ]]>'}}</g:price>
        </g:shipping>
        <g:google_product_category>
            @if(isset($category->parent_relation->name))
                {{'<![CDATA[ '.$category->parent_relation->name.'>'.$category->name.' ]]>'}}
            @else
                {{'<![CDATA[ '.$category->name.' ]]>'}}
            @endif
        </g:google_product_category>
        <g:custom_label_0>{{'<![CDATA[ '.'Made in Waterford, IE'.' ]]>'}}</g:custom_label_0>
        </item>
        @endforeach
        </channel>
    @endforeach
</rss>