<div class="bba-profile-info">
    <ul>
        {*会社名*}
        {if $cp_data.company_name}
            <li>
                <span class="bba-profile-info-label">{__("bba_com.community_profiles_company_name")}</span>
                <span class="bba-profile-info-value">{$cp_data.company_name}</span>
            </li>
        {/if}
        {*役職*}
        {if $cp_data.company_position}
            <li>
                <span class="bba-profile-info-label">{__("bba_com.community_profiles_company_position")}</span>
                <span class="bba-profile-info-value">{$cp_data.company_position}</span>
            </li>
        {/if}
        {*所在地*}
        {if $cp_data.company_address}
            <li>
                <span class="bba-profile-info-label">{__("bba_com.community_profiles_company_location")}</span>
                <span class="bba-profile-info-value">
                        〒{$cp_data.company_postal_code}<br>
                        {$cp_data.company_address}
                    </span>
            </li>
        {/if}
        {*事業内容*}
        {if $cp_data.business_content}
            <li>
                <span class="bba-profile-info-label">{__("bba_com.community_profiles_business_content")}</span>
                <span class="bba-profile-info-value">{$cp_data.business_content|nl2br nofilter}</span>
            </li>
        {/if}
        {*設⽴年⽉⽇*}
        {if $cp_data.company_established_date != '0000-00-00'}
            <li>
                <span class="bba-profile-info-label">{__("bba_com.community_profiles_company_established_date")}</span>
                <span class="bba-profile-info-value">{$cp_data.company_established_date}</span>
            </li>
        {/if}
        {*資本金*}
        {if $cp_data.company_capital}
            <li>
                <span class="bba-profile-info-label">{__("bba_com.community_profiles_company_capital")}</span>
                <span class="bba-profile-info-value">{$cp_data.company_capital}</span>
            </li>
        {/if}
        {*社員数*}
        {if $cp_data.company_employees}
            <li>
                <span class="bba-profile-info-label">{__("bba_com.community_profiles_company_employees")}</span>
                <span class="bba-profile-info-value">{$cp_data.company_employees}</span>
            </li>
        {/if}
        {*会社URL*}
        {if $cp_data.company_url}
            <li>
                <span class="bba-profile-info-label">{__("bba_com.community_profiles_company_url")}</span>
                <span class="bba-profile-info-value">
                        <a class="cm-new-window" href="{$cp_data.company_url}"
                           target="_blank">{$cp_data.company_url}</a>
                    </span>
            </li>
        {/if}
    </ul>
</div>