<template>
    <div class="container-fluid">

        <div class="st-page-header">
            <div>
                <h4 class="st-page-title">Site Settings</h4>
                <p class="st-page-subtitle">Manage your website configuration and preferences</p>
            </div>
        </div>

        <div class="st-card">

            <!-- ── Tab bar ──────────────────────────────────────────────── -->
            <div class="st-tabbar">
                <button class="st-tab" :class="{ active: tab === 'basic' }"   @click="tab = 'basic'">
                    <i class="icon-home"></i> Basic
                </button>
                <button class="st-tab" :class="{ active: tab === 'seo' }"     @click="tab = 'seo'">
                    <i class="icon-magnifier"></i> SEO
                </button>
                <button class="st-tab" :class="{ active: tab === 'social' }"  @click="tab = 'social'">
                    <i class="icon-social-facebook"></i> Social Links
                </button>
                <button class="st-tab" :class="{ active: tab === 'payment' }" @click="tab = 'payment'">
                    <i class="icon-credit-card"></i> Payment
                </button>
                <button class="st-tab" :class="{ active: tab === 'smtp' }"    @click="tab = 'smtp'">
                    <i class="icon-envelope"></i> SMTP
                </button>
            </div>

            <!-- ── Basic Settings ───────────────────────────────────────── -->
            <div v-if="tab === 'basic'" class="st-panel">
                <form @submit.prevent="SiteSettingsHandler" enctype="multipart/form-data">

                    <div class="st-section">
                        <div class="st-section-title"><i class="icon-info"></i> General Information</div>
                        <div class="st-grid-2">
                            <div class="st-field">
                                <label>Site Name</label>
                                <input name="site_name" class="form-control" type="text"
                                    placeholder="Enter site name" :value="get_setting_value('site_name')" />
                            </div>
                            <div class="st-field">
                                <label>Slogan</label>
                                <input name="slogan" class="form-control" type="text"
                                    placeholder="Your site tagline" :value="get_setting_value('slogan')" />
                            </div>
                            <div class="st-field">
                                <label>Email Address</label>
                                <input name="email" class="form-control" type="email"
                                    placeholder="info@example.com" :value="get_setting_value('email')" />
                            </div>
                            <div class="st-field">
                                <label>Phone Number</label>
                                <input name="phone_number" class="form-control" type="text"
                                    placeholder="+1 234 567 8900" :value="get_setting_value('phone_numbers')" />
                            </div>
                            <div class="st-field">
                                <label>Website URL</label>
                                <input name="website" class="form-control" type="text"
                                    placeholder="https://example.com" :value="get_setting_value('website')" />
                            </div>
                            <div class="st-field">
                                <label>Fax Number <span class="st-hint">optional</span></label>
                                <input name="fax_number" class="form-control" type="text"
                                    placeholder="+1 234 567 8901" :value="get_setting_value('fax_number')" />
                            </div>
                            <div class="st-field st-full">
                                <label>Address</label>
                                <textarea name="address" class="form-control" rows="2"
                                    placeholder="Enter full address" :value="get_setting_value('address')"></textarea>
                            </div>
                            <div class="st-field st-full">
                                <label>Marquee Text</label>
                                <input name="marquee" class="form-control" type="text"
                                    placeholder="Scrolling announcement text..." :value="get_setting_value('marquee')" />
                            </div>
                        </div>
                    </div>

                    <div class="st-section">
                        <div class="st-section-title"><i class="icon-clock"></i> Business Hours</div>
                        <div class="st-grid-2">
                            <div class="st-field">
                                <label>Opening Time</label>
                                <input name="opening_time" class="form-control" type="time"
                                    :value="get_setting_value('opening_time')" />
                            </div>
                            <div class="st-field">
                                <label>Closing Time</label>
                                <input name="closing_time" class="form-control" type="time"
                                    :value="get_setting_value('closing_time')" />
                            </div>
                        </div>
                    </div>

                    <div class="st-section">
                        <div class="st-section-title"><i class="icon-picture"></i> Branding & Media</div>
                        <div class="st-grid-3">
                            <div class="st-field">
                                <label>Site Logo</label>
                                <input class="form-control" name="image" type="file" accept="image/*" />
                                <div v-if="get_setting_value('image')" class="st-img-preview">
                                    <img :src="get_setting_value('image')" alt="Site Logo" />
                                </div>
                            </div>
                            <div class="st-field">
                                <label>Header Logo</label>
                                <input class="form-control" name="header_logo" type="file" accept="image/*" />
                                <div v-if="get_setting_value('header_logo')" class="st-img-preview">
                                    <img :src="get_setting_value('header_logo')" alt="Header Logo" />
                                </div>
                            </div>
                            <div class="st-field">
                                <label>Footer Logo</label>
                                <input class="form-control" name="footer_logo" type="file" accept="image/*" />
                                <div v-if="get_setting_value('footer_logo')" class="st-img-preview">
                                    <img :src="get_setting_value('footer_logo')" alt="Footer Logo" />
                                </div>
                            </div>
                            <div class="st-field">
                                <label>Favicon</label>
                                <input class="form-control" name="fabicon" type="file" accept="image/*" />
                                <div v-if="get_setting_value('fabicon')" class="st-img-preview st-favicon-preview">
                                    <img :src="get_setting_value('fabicon')" alt="Favicon" />
                                </div>
                            </div>
                            <div class="st-field st-full">
                                <label>Copyright Text</label>
                                <input name="copy_right" class="form-control" type="text"
                                    placeholder="© 2024-25 My Site. All rights reserved."
                                    :value="get_setting_value('copy_right')" />
                            </div>
                        </div>
                    </div>

                    <div class="st-actions">
                        <button type="submit" class="btn btn-primary"><i class="icon-check"></i> Save Basic Settings</button>
                    </div>
                </form>
            </div>

            <!-- ── SEO Settings ─────────────────────────────────────────── -->
            <div v-if="tab === 'seo'" class="st-panel">
                <form @submit.prevent="SiteSettingsHandler">

                    <div class="st-section">
                        <div class="st-section-title"><i class="icon-magnifier"></i> Basic SEO</div>
                        <div class="st-grid-1">
                            <div class="st-field">
                                <label>Meta Title</label>
                                <input name="meta_title" class="form-control" type="text"
                                    placeholder="Page title (50–60 characters recommended)"
                                    :value="get_setting_value('meta_title')" />
                            </div>
                            <div class="st-field">
                                <label>Meta Description</label>
                                <textarea name="meta_description" class="form-control" rows="3"
                                    placeholder="Page description (150–160 characters recommended)"
                                    :value="get_setting_value('meta_description')"></textarea>
                            </div>
                            <div class="st-field">
                                <label>Meta Keywords <span class="st-hint">comma-separated</span></label>
                                <input name="meta_keywords" class="form-control" type="text"
                                    placeholder="keyword1, keyword2, keyword3"
                                    :value="get_setting_value('meta_keywords')" />
                            </div>
                            <div class="st-field">
                                <label>Canonical URL</label>
                                <input name="canonical_url" class="form-control" type="text"
                                    placeholder="https://example.com"
                                    :value="get_setting_value('canonical_url')" />
                            </div>
                            <div class="st-field">
                                <label>Robots Meta</label>
                                <select name="robots_meta" class="form-control">
                                    <option value="index, follow"    :selected="get_setting_value('robots_meta') === 'index, follow'">index, follow</option>
                                    <option value="noindex, follow"  :selected="get_setting_value('robots_meta') === 'noindex, follow'">noindex, follow</option>
                                    <option value="index, nofollow"  :selected="get_setting_value('robots_meta') === 'index, nofollow'">index, nofollow</option>
                                    <option value="noindex, nofollow" :selected="get_setting_value('robots_meta') === 'noindex, nofollow'">noindex, nofollow</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="st-section">
                        <div class="st-section-title"><i class="icon-share"></i> Open Graph (Facebook / LinkedIn)</div>
                        <div class="st-grid-1">
                            <div class="st-field">
                                <label>OG Title</label>
                                <input name="og_title" class="form-control" type="text"
                                    placeholder="Title for social sharing"
                                    :value="get_setting_value('og_title')" />
                            </div>
                            <div class="st-field">
                                <label>OG Description</label>
                                <textarea name="og_description" class="form-control" rows="2"
                                    placeholder="Description for social sharing"
                                    :value="get_setting_value('og_description')"></textarea>
                            </div>
                            <div class="st-field">
                                <label>OG Image URL</label>
                                <input name="og_image" class="form-control" type="text"
                                    placeholder="https://example.com/og-image.jpg"
                                    :value="get_setting_value('og_image')" />
                            </div>
                        </div>
                    </div>

                    <div class="st-section">
                        <div class="st-section-title"><i class="icon-social-twitter"></i> Twitter Card</div>
                        <div class="st-grid-2">
                            <div class="st-field">
                                <label>Card Type</label>
                                <select name="twitter_card" class="form-control">
                                    <option value="summary_large_image" :selected="get_setting_value('twitter_card') === 'summary_large_image'">Summary Large Image</option>
                                    <option value="summary"             :selected="get_setting_value('twitter_card') === 'summary'">Summary</option>
                                </select>
                            </div>
                            <div class="st-field">
                                <label>Twitter Site Handle <span class="st-hint">@username</span></label>
                                <input name="twitter_site" class="form-control" type="text"
                                    placeholder="@yourhandle"
                                    :value="get_setting_value('twitter_site')" />
                            </div>
                        </div>
                    </div>

                    <div class="st-section">
                        <div class="st-section-title"><i class="icon-chart"></i> Analytics & Tracking</div>
                        <div class="st-grid-2">
                            <div class="st-field">
                                <label>Google Analytics ID</label>
                                <input name="google_analytics_id" class="form-control" type="text"
                                    placeholder="G-XXXXXXXXXX"
                                    :value="get_setting_value('google_analytics_id')" />
                            </div>
                            <div class="st-field">
                                <label>Google Tag Manager ID</label>
                                <input name="google_tag_manager_id" class="form-control" type="text"
                                    placeholder="GTM-XXXXXXX"
                                    :value="get_setting_value('google_tag_manager_id')" />
                            </div>
                            <div class="st-field">
                                <label>Google Search Console Code</label>
                                <input name="google_search_console_code" class="form-control" type="text"
                                    placeholder="Verification meta content value"
                                    :value="get_setting_value('google_search_console_code')" />
                            </div>
                            <div class="st-field">
                                <label>Facebook Pixel ID</label>
                                <input name="facebook_pixel_id" class="form-control" type="text"
                                    placeholder="XXXXXXXXXXXXXXXXXX"
                                    :value="get_setting_value('facebook_pixel_id')" />
                            </div>
                        </div>
                    </div>

                    <div class="st-actions">
                        <button type="submit" class="btn btn-primary"><i class="icon-check"></i> Save SEO Settings</button>
                    </div>
                </form>
            </div>

            <!-- ── Social Links ─────────────────────────────────────────── -->
            <div v-if="tab === 'social'" class="st-panel">
                <form @submit.prevent="SiteSettingsHandler">

                    <div class="st-section">
                        <div class="st-section-title"><i class="icon-social-facebook"></i> Primary Platforms</div>
                        <div class="st-grid-2">
                            <div class="st-field">
                                <label><span class="st-badge st-fb">f</span> Facebook</label>
                                <input name="facebook" class="form-control" type="text"
                                    placeholder="https://facebook.com/yourpage"
                                    :value="get_setting_value('facebook')" />
                            </div>
                            <div class="st-field">
                                <label><span class="st-badge st-tw">𝕏</span> Twitter / X</label>
                                <input name="twitter" class="form-control" type="text"
                                    placeholder="https://twitter.com/yourprofile"
                                    :value="get_setting_value('twitter')" />
                            </div>
                            <div class="st-field">
                                <label><span class="st-badge st-ig">▣</span> Instagram</label>
                                <input name="instagram" class="form-control" type="text"
                                    placeholder="https://instagram.com/yourprofile"
                                    :value="get_setting_value('instagram')" />
                            </div>
                            <div class="st-field">
                                <label><span class="st-badge st-li">in</span> LinkedIn</label>
                                <input name="linkedin" class="form-control" type="text"
                                    placeholder="https://linkedin.com/in/yourprofile"
                                    :value="get_setting_value('linkedin')" />
                            </div>
                            <div class="st-field">
                                <label><span class="st-badge st-yt">▶</span> YouTube</label>
                                <input name="youtube" class="form-control" type="text"
                                    placeholder="https://youtube.com/yourchannel"
                                    :value="get_setting_value('youtube')" />
                            </div>
                            <div class="st-field">
                                <label><span class="st-badge st-tk">♪</span> TikTok</label>
                                <input name="tiktok" class="form-control" type="text"
                                    placeholder="https://tiktok.com/@yourprofile"
                                    :value="get_setting_value('tiktok')" />
                            </div>
                            <div class="st-field">
                                <label><span class="st-badge st-pin">P</span> Pinterest</label>
                                <input name="pinterest" class="form-control" type="text"
                                    placeholder="https://pinterest.com/yourprofile"
                                    :value="get_setting_value('pinterest')" />
                            </div>
                            <div class="st-field">
                                <label><span class="st-badge st-sc">👻</span> Snapchat</label>
                                <input name="snapchat" class="form-control" type="text"
                                    placeholder="https://snapchat.com/add/yourprofile"
                                    :value="get_setting_value('snapchat')" />
                            </div>
                        </div>
                    </div>

                    <div class="st-section">
                        <div class="st-section-title"><i class="icon-bubble"></i> Messaging & Community</div>
                        <div class="st-grid-2">
                            <div class="st-field">
                                <label><span class="st-badge st-wa">W</span> WhatsApp</label>
                                <input name="whatsapp" class="form-control" type="text"
                                    placeholder="https://wa.me/1234567890"
                                    :value="get_setting_value('whatsapp')" />
                            </div>
                            <div class="st-field">
                                <label><span class="st-badge st-tg">✈</span> Telegram</label>
                                <input name="telegram" class="form-control" type="text"
                                    placeholder="https://t.me/yourchannel"
                                    :value="get_setting_value('telegram')" />
                            </div>
                            <div class="st-field">
                                <label><span class="st-badge st-gh">⌥</span> GitHub</label>
                                <input name="github" class="form-control" type="text"
                                    placeholder="https://github.com/yourprofile"
                                    :value="get_setting_value('github')" />
                            </div>
                            <div class="st-field">
                                <label><span class="st-badge st-dc">◈</span> Discord</label>
                                <input name="discord" class="form-control" type="text"
                                    placeholder="https://discord.gg/yourserver"
                                    :value="get_setting_value('discord')" />
                            </div>
                        </div>
                    </div>

                    <div class="st-actions">
                        <button type="submit" class="btn btn-primary"><i class="icon-check"></i> Save Social Links</button>
                    </div>
                </form>
            </div>

            <!-- ── Payment Settings ─────────────────────────────────────── -->
            <div v-if="tab === 'payment'" class="st-panel">
                <form @submit.prevent="SiteSettingsHandler">

                    <div class="st-section">
                        <div class="st-section-title"><i class="icon-screen-smartphone"></i> Mobile Banking</div>
                        <div class="st-grid-3">
                            <!-- bKash -->
                            <div class="st-payment-card">
                                <div class="st-payment-card-header st-bkash">bKash</div>
                                <div class="st-field">
                                    <label>Account Number</label>
                                    <input name="bkash_account_number" class="form-control" type="text"
                                        placeholder="01XXXXXXXXX"
                                        :value="get_setting_value('bkash_account_number')" />
                                </div>
                                <div class="st-field">
                                    <label>Account Type</label>
                                    <select name="bkash_account_type" class="form-control">
                                        <option value="Personal" :selected="get_setting_value('bkash_account_type') === 'Personal'">Personal</option>
                                        <option value="Merchant" :selected="get_setting_value('bkash_account_type') === 'Merchant'">Merchant</option>
                                        <option value="Agent"    :selected="get_setting_value('bkash_account_type') === 'Agent'">Agent</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Nagad -->
                            <div class="st-payment-card">
                                <div class="st-payment-card-header st-nagad">Nagad</div>
                                <div class="st-field">
                                    <label>Account Number</label>
                                    <input name="nagad_account_number" class="form-control" type="text"
                                        placeholder="01XXXXXXXXX"
                                        :value="get_setting_value('nagad_account_number')" />
                                </div>
                                <div class="st-field">
                                    <label>Account Type</label>
                                    <select name="nagad_account_type" class="form-control">
                                        <option value="Personal" :selected="get_setting_value('nagad_account_type') === 'Personal'">Personal</option>
                                        <option value="Merchant" :selected="get_setting_value('nagad_account_type') === 'Merchant'">Merchant</option>
                                        <option value="Agent"    :selected="get_setting_value('nagad_account_type') === 'Agent'">Agent</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Rocket -->
                            <div class="st-payment-card">
                                <div class="st-payment-card-header st-rocket">Rocket</div>
                                <div class="st-field">
                                    <label>Account Number</label>
                                    <input name="rocket_account_number" class="form-control" type="text"
                                        placeholder="01XXXXXXXXX"
                                        :value="get_setting_value('rocket_account_number')" />
                                </div>
                                <div class="st-field">
                                    <label>Account Type</label>
                                    <select name="rocket_account_type" class="form-control">
                                        <option value="Personal" :selected="get_setting_value('rocket_account_type') === 'Personal'">Personal</option>
                                        <option value="Merchant" :selected="get_setting_value('rocket_account_type') === 'Merchant'">Merchant</option>
                                        <option value="Agent"    :selected="get_setting_value('rocket_account_type') === 'Agent'">Agent</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="st-section">
                        <div class="st-section-title"><i class="icon-credit-card"></i> Bank Account</div>
                        <div class="st-grid-2">
                            <div class="st-field">
                                <label>Bank Name</label>
                                <input name="bank_name" class="form-control" type="text"
                                    placeholder="e.g. Dutch Bangla Bank"
                                    :value="get_setting_value('bank_name')" />
                            </div>
                            <div class="st-field">
                                <label>Account Holder Name</label>
                                <input name="bank_account_name" class="form-control" type="text"
                                    placeholder="Account holder name"
                                    :value="get_setting_value('bank_account_name')" />
                            </div>
                            <div class="st-field">
                                <label>Account Number</label>
                                <input name="bank_account_number" class="form-control" type="text"
                                    placeholder="e.g. 1234567890"
                                    :value="get_setting_value('bank_account_number')" />
                            </div>
                            <div class="st-field">
                                <label>Branch Name</label>
                                <input name="bank_branch_name" class="form-control" type="text"
                                    placeholder="e.g. Mirpur Branch"
                                    :value="get_setting_value('bank_branch_name')" />
                            </div>
                            <div class="st-field">
                                <label>Routing Number</label>
                                <input name="bank_routing_number" class="form-control" type="text"
                                    placeholder="e.g. 123456789"
                                    :value="get_setting_value('bank_routing_number')" />
                            </div>
                        </div>
                    </div>

                    <div class="st-section">
                        <div class="st-section-title"><i class="icon-lock"></i> Payment Gateway</div>
                        <div class="st-grid-2">
                            <div class="st-field">
                                <label>Gateway</label>
                                <select name="payment_gateway" class="form-control">
                                    <option value="sslcommerz" :selected="get_setting_value('payment_gateway') === 'sslcommerz'">SSL Commerz</option>
                                    <option value="stripe"     :selected="get_setting_value('payment_gateway') === 'stripe'">Stripe</option>
                                    <option value="paypal"     :selected="get_setting_value('payment_gateway') === 'paypal'">PayPal</option>
                                    <option value="razorpay"   :selected="get_setting_value('payment_gateway') === 'razorpay'">Razorpay</option>
                                </select>
                            </div>
                            <div class="st-field">
                                <label>Mode</label>
                                <select name="payment_gateway_mode" class="form-control">
                                    <option value="sandbox" :selected="get_setting_value('payment_gateway_mode') === 'sandbox'">Sandbox (Test)</option>
                                    <option value="live"    :selected="get_setting_value('payment_gateway_mode') === 'live'">Live</option>
                                </select>
                            </div>
                            <div class="st-field">
                                <label>Store ID / API Key</label>
                                <input name="payment_store_id" class="form-control" type="text"
                                    placeholder="Store ID or API Key"
                                    :value="get_setting_value('payment_store_id')" />
                            </div>
                            <div class="st-field">
                                <label>Store Password / Secret Key</label>
                                <input name="payment_store_password" class="form-control" type="password"
                                    placeholder="••••••••"
                                    :value="get_setting_value('payment_store_password')" />
                            </div>
                        </div>
                    </div>

                    <div class="st-section">
                        <div class="st-section-title"><i class="icon-credit-card"></i> Accepted Cards</div>
                        <div class="st-grid-3">
                            <div class="st-field">
                                <label>Visa</label>
                                <select name="accept_visa" class="form-control">
                                    <option value="yes" :selected="get_setting_value('accept_visa') === 'yes'">Yes</option>
                                    <option value="no"  :selected="get_setting_value('accept_visa') === 'no'">No</option>
                                </select>
                            </div>
                            <div class="st-field">
                                <label>Mastercard</label>
                                <select name="accept_mastercard" class="form-control">
                                    <option value="yes" :selected="get_setting_value('accept_mastercard') === 'yes'">Yes</option>
                                    <option value="no"  :selected="get_setting_value('accept_mastercard') === 'no'">No</option>
                                </select>
                            </div>
                            <div class="st-field">
                                <label>American Express</label>
                                <select name="accept_amex" class="form-control">
                                    <option value="yes" :selected="get_setting_value('accept_amex') === 'yes'">Yes</option>
                                    <option value="no"  :selected="get_setting_value('accept_amex') === 'no'">No</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="st-actions">
                        <button type="submit" class="btn btn-primary"><i class="icon-check"></i> Save Payment Settings</button>
                    </div>
                </form>
            </div>

            <!-- ── SMTP Settings ─────────────────────────────────────────── -->
            <div v-if="tab === 'smtp'" class="st-panel">
                <form @submit.prevent="SiteSettingsHandler">

                    <div class="st-section">
                        <div class="st-section-title"><i class="icon-envelope"></i> Mail Server Configuration</div>
                        <div class="st-grid-2">
                            <div class="st-field">
                                <label>SMTP Host</label>
                                <input name="mail_host" class="form-control" type="text"
                                    placeholder="smtp.mailserver.com"
                                    :value="get_setting_value('mail_host')" />
                            </div>
                            <div class="st-field">
                                <label>SMTP Port</label>
                                <input name="mail_port" class="form-control" type="text"
                                    placeholder="465"
                                    :value="get_setting_value('mail_port')" />
                            </div>
                            <div class="st-field">
                                <label>Username</label>
                                <input name="mail_username" class="form-control" type="text"
                                    placeholder="your-email@example.com"
                                    :value="get_setting_value('mail_username')" />
                            </div>
                            <div class="st-field">
                                <label>Password</label>
                                <input name="mail_password" class="form-control" type="password"
                                    placeholder="••••••••"
                                    :value="get_setting_value('mail_password')" />
                            </div>
                            <div class="st-field">
                                <label>Encryption</label>
                                <select name="mail_encryption" class="form-control">
                                    <option value="ssl" :selected="get_setting_value('mail_encryption') === 'ssl'">SSL</option>
                                    <option value="tls" :selected="get_setting_value('mail_encryption') === 'tls'">TLS</option>
                                </select>
                            </div>
                            <div class="st-field">
                                <label>From Address</label>
                                <input name="mail_from_address" class="form-control" type="email"
                                    placeholder="no-reply@example.com"
                                    :value="get_setting_value('mail_from_address')" />
                            </div>
                            <div class="st-field st-full">
                                <label>From Name</label>
                                <input name="mail_from_name" class="form-control" type="text"
                                    placeholder="Your Application Name"
                                    :value="get_setting_value('mail_from_name')" />
                            </div>
                        </div>
                    </div>

                    <div class="st-actions">
                        <button type="submit" class="btn btn-primary"><i class="icon-check"></i> Save SMTP Settings</button>
                    </div>
                </form>
            </div>

        </div><!-- /.st-card -->
    </div>
</template>

<script>
import { auth_store } from "../../../../GlobalStore/auth_store";
import { site_settings_store } from "../../../../GlobalStore/site_settings_store";
import { mapState, mapActions } from "pinia";
export default {
    data: () => ({
        tab: "basic",
    }),
    methods: {
        ...mapActions(auth_store, { check_is_auth: "check_is_auth" }),
        ...mapActions(site_settings_store, {
            get_all_website_settings: "get_all_website_settings",
            get_setting_value: "get_setting_value",
        }),
        SiteSettingsHandler: async function (event) {
            let formData = new FormData(event.target);
            let response = await axios.post("website-settings/store", formData);
            if (response.data.status == "success") {
                window.s_alert(response.data.message);
                this.get_all_website_settings();
            }
        },
    },
    computed: {
        ...mapState(auth_store, { auth_info: "auth_info" }),
        ...mapState(site_settings_store, { website_settings_data: "website_settings_data" }),
    },
};
</script>

<style scoped>
/* ── Page header ─────────────────────────────────────────────────────── */
.st-page-header { padding: 16px 0 12px; }
.st-page-title  { margin: 0; font-size: 1.2rem; font-weight: 600; color: var(--text-primary); }
.st-page-subtitle { margin: 2px 0 0; font-size: 0.8rem; color: var(--text-light); }

/* ── Card ────────────────────────────────────────────────────────────── */
.st-card {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 24px;
}

/* ── Tab bar ─────────────────────────────────────────────────────────── */
.st-tabbar {
    display: flex;
    border-bottom: 1px solid var(--border-color);
    padding: 0 16px;
    gap: 2px;
    overflow-x: auto;
}
.st-tab {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 13px 18px;
    border: none;
    background: transparent;
    color: var(--text-secondary);
    font-size: 0.875rem;
    cursor: pointer;
    border-bottom: 2px solid transparent;
    margin-bottom: -1px;
    white-space: nowrap;
    transition: color 0.15s, border-color 0.15s;
}
.st-tab i { font-size: 0.9rem; }
.st-tab:hover { color: var(--text-primary); }
.st-tab.active { color: var(--primary-color); border-bottom-color: var(--primary-color); font-weight: 500; }

/* ── Panel ───────────────────────────────────────────────────────────── */
.st-panel { padding: 24px 28px 8px; }

/* ── Section ─────────────────────────────────────────────────────────── */
.st-section { margin-bottom: 28px; }
.st-section-title {
    display: flex; align-items: center; gap: 7px;
    font-size: 0.75rem; font-weight: 600; text-transform: uppercase;
    letter-spacing: 0.07em; color: var(--text-light);
    margin-bottom: 16px; padding-bottom: 8px;
    border-bottom: 1px solid var(--border-color);
}

/* ── Grids ───────────────────────────────────────────────────────────── */
.st-grid-1 { display: grid; grid-template-columns: 1fr; gap: 14px; }
.st-grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; }
.st-grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
.st-full   { grid-column: 1 / -1; }

/* ── Field ───────────────────────────────────────────────────────────── */
.st-field label {
    display: block; font-size: 0.8rem; font-weight: 500;
    color: var(--text-secondary); margin-bottom: 5px;
}
.st-hint { font-weight: 400; font-size: 0.72rem; color: var(--text-light); margin-left: 4px; }

/* ── Social badges ───────────────────────────────────────────────────── */
.st-badge {
    display: inline-flex; align-items: center; justify-content: center;
    width: 18px; height: 18px; border-radius: 4px;
    font-size: 0.65rem; font-weight: 700; color: #fff;
    margin-right: 4px; vertical-align: middle;
}
.st-fb  { background: #1877f2; }
.st-tw  { background: #000; }
.st-ig  { background: radial-gradient(circle at 30% 107%, #fdf497 0%, #fd5949 45%, #d6249f 60%, #285aeb 90%); }
.st-li  { background: #0a66c2; }
.st-yt  { background: #ff0000; }
.st-tk  { background: #010101; }
.st-pin { background: #e60023; }
.st-sc  { background: #fffc00; color: #000; }
.st-wa  { background: #25d366; }
.st-tg  { background: #2ca5e0; }
.st-gh  { background: #24292e; }
.st-dc  { background: #5865f2; }

/* ── Payment cards ───────────────────────────────────────────────────── */
.st-payment-card {
    border: 1px solid var(--border-color);
    border-radius: 8px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding-bottom: 14px;
}
.st-payment-card-header {
    padding: 9px 14px;
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    color: #fff;
}
.st-payment-card .st-field { padding: 0 14px; }
.st-bkash  { background: #e2136e; }
.st-nagad  { background: #f15a22; }
.st-rocket { background: #8b1a8b; }

/* ── Image preview ───────────────────────────────────────────────────── */
.st-img-preview {
    margin-top: 8px; padding: 8px;
    border: 1px dashed var(--border-color);
    border-radius: 6px; display: inline-block;
    background: var(--bg-hover);
}
.st-img-preview img     { height: 60px; width: auto; display: block; object-fit: contain; }
.st-favicon-preview img { height: 32px; width: 32px; object-fit: contain; }

/* ── Save row ────────────────────────────────────────────────────────── */
.st-actions {
    padding: 16px 0 20px;
    border-top: 1px solid var(--border-color);
}
.st-actions .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 22px; font-size: 0.875rem; }

/* ── Responsive ──────────────────────────────────────────────────────── */
@media (max-width: 768px) {
    .st-grid-2, .st-grid-3 { grid-template-columns: 1fr; }
    .st-panel { padding: 16px 16px 8px; }
}
</style>
