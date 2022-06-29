<?php echo '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL; ?>
<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
    @foreach($categories as $category)
        <channel>
        <title>{{$category->name}}</title>
        <link>{{$domain->full_domain}}</link>
        @foreach($category->take_20_product as $row)
        @php 
        $content = json_decode($row->content->value);
        $preview = json_decode($row->preview);
        $brands = $row->brands;
        $currency = currency_info();
        $currency = $currency['currency_default']->currency_name;
        @endphp
        <item>
        <id>{{'<![CDATA[ '.$row->id.' ]]>'}}</id>
        <title>{{'<![CDATA[ '.$row->title.' ]]>'}}</title>
        <description>{{'<![CDATA[ '.strip_tags($content->content).' ]]>'}}</description>
        <link>{{'<![CDATA[ '.$domain->full_domain.'/product/'.$row->slug.'/'.$row->id.' ]]>'}}</link>
        <image_link>@if(isset($preview->media)){{'<![CDATA['.'https:'.$preview->media->url.' ]]>'}}@endif</image_link>
        <brand>@if(count($brands) > 0){{'<![CDATA['.$brands.' ]]>'}} @endif</brand>
        <condition>new</condition>
        <availability>@if($row->featured){{'<![CDATA[ in stock ]]>' }}@else {{'<![CDATA[ out of stock ]]>'}} @endif</availability>
        <price>{{'<![CDATA['.$currency.' '.$row->price->price.']]>'}}</price>
        <shipping>
        <country>{{'<![CDATA[ '.'UK'.' ]]>'}}</country>
        <service>{{'<![CDATA[ '.'Standard'.' ]]>'}}</service>
        <price>{{'<![CDATA[ '.'4.95 GBP'.' ]]>'}}</price>
        </shipping>
        <google_product_category>
            @if(isset($category->parent_relation->name))
                {{'<![CDATA[ '.$category->parent_relation->name.'>'.$category->name.' ]]>'}}
            @else
                {{'<![CDATA[ '.$category->name.' ]]>'}}
            @endif
        </google_product_category>
        <custom_label_0>{{'<![CDATA[ '.'Made in Waterford, IE'.' ]]>'}}</custom_label_0>
        </item>
        @endforeach
        </channel>
    @endforeach
</rss>