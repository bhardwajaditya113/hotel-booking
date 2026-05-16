@php
    /** @var \App\Models\Property|null $listing */
    $isEdit = $listing !== null;

    $listingCountry = old('country', $listing?->country ?? 'India');
    $persistedState = $listing ? ($listing->state ?? '') : '';
    $stateOld = old('state', $persistedState);
    $stateIsIndianPreset = $stateOld !== '' && in_array($stateOld, $indianStatesUt, true);
    $useIndiaOther = ($listingCountry === 'India') && $stateOld !== '' && ! $stateIsIndianPreset;
    $indiaSelectValue = ($listingCountry === 'India')
        ? ($useIndiaOther ? '__other__' : ($stateOld ?: ''))
        : '';

    $amenityDefaults = ($listing && is_array($listing->amenities))
        ? array_map('intval', $listing->amenities)
        : [];
    $selectedAmenities = array_map('intval', (array) old('amenities', $amenityDefaults));

    $defaultCheckIn = $listing && $listing->check_in_time
        ? substr((string) $listing->check_in_time, 0, 5)
        : '14:00';
    $defaultCheckOut = $listing && $listing->check_out_time
        ? substr((string) $listing->check_out_time, 0, 5)
        : '11:00';

    $cancellationPresetDefault = $listing
        ? $listing->inferCancellationPreset()
        : 'moderate';

    $_countryOld = $listingCountry;
    $_isIndia = $_countryOld === 'India';
@endphp

{{-- Basics --}}
<div class="nx-host-listing-section card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4 p-lg-5">
        <div class="d-flex align-items-start gap-3 mb-4">
            <div class="nx-host-listing-step-badge">1</div>
            <div>
                <h3 class="h5 fw-bold mb-1">{{ __('frontend.host_listing.section_basics') }}</h3>
                <p class="text-muted small mb-0">{{ __('frontend.host_listing.section_basics_sub') }}</p>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">{{ __('frontend.host_listing.field_name') }} <span class="text-danger">*</span></label>
            <input type="text" name="name" value="{{ old('name', $listing?->name ?? '') }}" class="form-control form-control-lg rounded-4" required maxlength="255"
                   placeholder="{{ __('frontend.host_listing.field_name') }}">
        </div>

        @if (! $isEdit)
            <div class="mb-2 fw-semibold">{{ __('frontend.host_listing.field_listing_type') }} <span class="text-danger">*</span></div>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <input type="radio" class="btn-check" name="listing_type" id="listing-type-hotel" value="hotel" autocomplete="off"
                           {{ old('listing_type', 'hotel') === 'hotel' ? 'checked' : '' }} required>
                    <label class="btn btn-outline-secondary border-2 rounded-4 p-4 h-100 text-start nx-host-listing-type-card w-100" for="listing-type-hotel">
                        <span class="d-flex align-items-start gap-3">
                            <span class="nx-host-listing-type-icon"><i class="bx bx-building fs-3"></i></span>
                            <span>
                                <span class="fw-bold d-block">{{ __('frontend.host_listing.listing_hotel_title') }}</span>
                                <span class="small text-muted">{{ __('frontend.host_listing.listing_hotel_body') }}</span>
                            </span>
                        </span>
                    </label>
                </div>
                <div class="col-md-6">
                    <input type="radio" class="btn-check" name="listing_type" id="listing-type-unique" value="unique_stay" autocomplete="off"
                           {{ old('listing_type') === 'unique_stay' ? 'checked' : '' }}>
                    <label class="btn btn-outline-secondary border-2 rounded-4 p-4 h-100 text-start nx-host-listing-type-card w-100" for="listing-type-unique">
                        <span class="d-flex align-items-start gap-3">
                            <span class="nx-host-listing-type-icon"><i class="bx bx-home-heart fs-3"></i></span>
                            <span>
                                <span class="fw-bold d-block">{{ __('frontend.host_listing.listing_unique_title') }}</span>
                                <span class="small text-muted">{{ __('frontend.host_listing.listing_unique_body') }}</span>
                            </span>
                        </span>
                    </label>
                </div>
            </div>
        @else
            <div class="mb-4">
                <label class="form-label fw-semibold">{{ __('frontend.host_listing.field_listing_type') }}</label>
                <input type="text" class="form-control form-control-lg rounded-4 bg-light" readonly
                       value="{{ ucfirst(str_replace('_', ' ', $listing->listing_type ?? '')) }}">
                <p class="small text-muted mt-2 mb-0">{{ __('frontend.host_listing.listing_type_locked') }}</p>
            </div>
        @endif

        <div class="mb-0">
            <label class="form-label fw-semibold">{{ __('frontend.host_listing.field_property_type') }} <span class="text-danger">*</span></label>
            <select name="property_type_id" class="form-select form-select-lg rounded-4" required @disabled($types->isEmpty())>
                <option value="">{{ __('frontend.host_listing.select_placeholder_property_type') }}</option>
                @foreach ($types as $type)
                    <option value="{{ $type->id }}" @selected(old('property_type_id', $listing?->property_type_id ?? '') == $type->id)>{{ $type->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

{{-- Location --}}
<div class="nx-host-listing-section card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4 p-lg-5">
        <div class="d-flex align-items-start gap-3 mb-4">
            <div class="nx-host-listing-step-badge">2</div>
            <div>
                <h3 class="h5 fw-bold mb-1">{{ __('frontend.host_listing.section_location') }}</h3>
                <p class="text-muted small mb-0">{{ __('frontend.host_listing.section_location_sub') }}</p>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">{{ __('frontend.host_listing.country') }} <span class="text-danger">*</span></label>
                <select name="country" id="nxListingCountry" class="form-select form-select-lg rounded-4" required>
                    @foreach ($countries as $countryName)
                        <option value="{{ $countryName }}" @selected($_countryOld === $countryName)>{{ $countryName }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">{{ __('frontend.host_listing.zipcode') }}</label>
                <input type="text" name="zipcode" value="{{ old('zipcode', $listing?->zipcode ?? '') }}" class="form-control form-control-lg rounded-4" maxlength="32">
            </div>
        </div>

        <div id="nxListingStateIndiaWrap" class="mb-3 {{ $_isIndia ? '' : 'd-none' }}">
            <label class="form-label fw-semibold">{{ __('frontend.host_listing.state_region') }}</label>
            <select id="nxListingStateIndiaSelect" class="form-select form-select-lg rounded-4"
                    @if ($_isIndia) name="state" @else disabled @endif>
                <option value="">{{ __('frontend.host_listing.state_placeholder_in') }}</option>
                @foreach ($indianStatesUt as $st)
                    <option value="{{ $st }}" @selected($indiaSelectValue === $st)>{{ $st }}</option>
                @endforeach
                <option value="__other__" @selected($useIndiaOther)>{{ __('frontend.host_listing.state_other') }}</option>
            </select>
            <div id="nxListingStateIndiaOtherWrap" class="mt-2 {{ $useIndiaOther ? '' : 'd-none' }}">
                <input type="text" id="nxListingStateIndiaOther" class="form-control rounded-4"
                       placeholder="{{ __('frontend.host_listing.state_placeholder_other') }}" maxlength="255"
                       value="{{ $useIndiaOther ? $stateOld : '' }}">
            </div>
        </div>

        <div id="nxListingStateGlobalWrap" class="mb-3 {{ $_isIndia ? 'd-none' : '' }}">
            <label class="form-label fw-semibold">{{ __('frontend.host_listing.state_region') }}</label>
            <input type="text" id="nxListingStateGlobalInput" class="form-control form-control-lg rounded-4"
                   placeholder="{{ __('frontend.host_listing.state_placeholder_other') }}" maxlength="255"
                   @if ($_isIndia) disabled @else name="state" @endif
                   value="{{ $_isIndia ? '' : $stateOld }}">
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">{{ __('frontend.host_listing.city') }} <span class="text-danger">*</span></label>
            <input type="text" name="city" value="{{ old('city', $listing?->city ?? '') }}" class="form-control form-control-lg rounded-4" required maxlength="255">
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">{{ __('frontend.host_listing.address_line') }} <span class="text-danger">*</span></label>
            <input type="text" name="address" value="{{ old('address', $listing?->address ?? '') }}" class="form-control form-control-lg rounded-4" required>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">{{ __('frontend.host_listing.latitude') }}</label>
                <input type="number" step="any" name="latitude" value="{{ old('latitude', $listing?->latitude ?? '') }}" class="form-control rounded-4" placeholder="19.0760">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">{{ __('frontend.host_listing.longitude') }}</label>
                <input type="number" step="any" name="longitude" value="{{ old('longitude', $listing->longitude ?? '') }}" class="form-control rounded-4" placeholder="72.8777">
            </div>
        </div>
        <p class="small text-muted mt-2 mb-0">{{ __('frontend.host_listing.coords_hint') }}</p>
    </div>
</div>

{{-- Guest experience --}}
<div class="nx-host-listing-section card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4 p-lg-5">
        <div class="d-flex align-items-start gap-3 mb-4">
            <div class="nx-host-listing-step-badge">3</div>
            <div>
                <h3 class="h5 fw-bold mb-1">{{ __('frontend.host_listing.section_guest') }}</h3>
                <p class="text-muted small mb-0">{{ __('frontend.host_listing.section_guest_sub') }}</p>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">{{ __('frontend.host_listing.check_in_default') }}</label>
                <input type="time" name="check_in_time" value="{{ old('check_in_time', $defaultCheckIn) }}" class="form-control form-control-lg rounded-4">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">{{ __('frontend.host_listing.check_out_default') }}</label>
                <input type="time" name="check_out_time" value="{{ old('check_out_time', $defaultCheckOut) }}" class="form-control form-control-lg rounded-4">
            </div>
        </div>

        <div class="mb-2 fw-semibold">{{ __('frontend.host_listing.amenities_heading') }}</div>
        <p class="small text-muted mb-3">{{ __('frontend.host_listing.amenities_sub') }}</p>

        @if ($amenityCategories->isEmpty() || $amenityCategories->every(fn ($c) => $c->activeAmenities->isEmpty()))
            <p class="small text-muted fst-italic mb-4">{{ __('frontend.host_listing.amenities_empty') }}</p>
        @else
            <div class="nx-host-amenity-board mb-4">
                @foreach ($amenityCategories as $category)
                    @if ($category->activeAmenities->isEmpty())
                        @continue
                    @endif
                    <div class="nx-host-amenity-group mb-4">
                        <div class="fw-semibold small text-uppercase text-muted mb-2" style="letter-spacing: 0.06em;">
                            {{ $category->name }}
                        </div>
                        <div class="row g-2">
                            @foreach ($category->activeAmenities as $amenity)
                                <div class="col-6 col-lg-4">
                                    <label class="nx-host-amenity-chip">
                                        <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}"
                                               class="form-check-input"
                                               {{ in_array($amenity->id, $selectedAmenities, true) ? 'checked' : '' }}>
                                        <span class="nx-host-amenity-chip-label">
                                            @if ($amenity->icon)
                                                <i class="fa-solid {{ $amenity->icon }} me-2 text-muted"></i>
                                            @endif
                                            {{ $amenity->name }}
                                        </span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mb-3">
            <label class="form-label fw-semibold">{{ __('frontend.host_listing.describe_place') }}</label>
            <textarea name="description" rows="5" class="form-control rounded-4" placeholder="{{ __('frontend.host_listing.describe_hint') }}">{{ old('description', $listing?->description ?? '') }}</textarea>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">{{ __('frontend.host_listing.contact_phone') }}</label>
                <input type="text" name="phone" value="{{ old('phone', $listing?->phone ?? '') }}" class="form-control rounded-4">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">{{ __('frontend.host_listing.contact_email') }}</label>
                <input type="email" name="email" value="{{ old('email', $listing?->email ?? '') }}" class="form-control rounded-4">
            </div>
        </div>

        <input type="hidden" name="instant_book_enabled" value="0">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" name="instant_book_enabled" id="instant_book_enabled" value="1"
                   {{ old('instant_book_enabled', ($listing && $listing->instant_book_enabled) ? '1' : (!$listing ? '1' : '0')) === '1' ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold" for="instant_book_enabled">{{ __('frontend.host_listing.instant_book') }}</label>
        </div>
        <p class="small text-muted mt-2 mb-0">{{ __('frontend.host_listing.instant_book_help') }}</p>
    </div>
</div>

{{-- Policies --}}
<div class="nx-host-listing-section card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4 p-lg-5">
        <div class="d-flex align-items-start gap-3 mb-4">
            <div class="nx-host-listing-step-badge">4</div>
            <div>
                <h3 class="h5 fw-bold mb-1">{{ __('frontend.host_listing.section_policies') }}</h3>
                <p class="text-muted small mb-0">{{ __('frontend.host_listing.section_policies_sub') }}</p>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">{{ __('frontend.host_listing.house_rules') }}</label>
            <textarea name="house_rules" rows="4" class="form-control rounded-4" placeholder="{{ __('frontend.host_listing.house_rules_placeholder') }}">{{ old('house_rules', $listing?->house_rules ?? '') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">{{ __('frontend.host_listing.cancellation_preset_label') }}</label>
            <select name="cancellation_preset" id="nxCancellationPreset" class="form-select form-select-lg rounded-4">
                <option value="flexible" @selected(old('cancellation_preset', $cancellationPresetDefault) === 'flexible')>{{ __('frontend.host_listing.cancellation_flexible') }}</option>
                <option value="moderate" @selected(old('cancellation_preset', $cancellationPresetDefault) === 'moderate')>{{ __('frontend.host_listing.cancellation_moderate') }}</option>
                <option value="firm" @selected(old('cancellation_preset', $cancellationPresetDefault) === 'firm')>{{ __('frontend.host_listing.cancellation_firm') }}</option>
                <option value="custom" @selected(old('cancellation_preset', $cancellationPresetDefault) === 'custom')>{{ __('frontend.host_listing.cancellation_custom') }}</option>
            </select>
        </div>

        <div id="nxCancellationCustomWrap" class="{{ old('cancellation_preset', $cancellationPresetDefault) === 'custom' ? '' : 'd-none' }}">
            <label class="form-label fw-semibold">{{ __('frontend.host_listing.cancellation_custom') }}</label>
            <textarea name="cancellation_policy_text" rows="4" class="form-control rounded-4" placeholder="{{ __('frontend.host_listing.cancellation_custom_placeholder') }}">{{ old('cancellation_policy_text', $listing?->cancellation_policy_text ?? '') }}</textarea>
            <p class="small text-muted mt-2">{{ __('frontend.host_listing.cancellation_custom_help') }}</p>
        </div>

        <details class="nx-host-listing-details border rounded-4 p-3 mt-4">
            <summary class="fw-semibold user-select-none">{{ __('frontend.host_listing.seo_heading') }}</summary>
            <p class="small text-muted mt-3 mb-3">{{ __('frontend.host_listing.seo_intro') }}</p>
            <div class="mb-3">
                <label class="form-label small fw-semibold">{{ __('frontend.host_listing.meta_title') }}</label>
                <input type="text" name="meta_title" value="{{ old('meta_title', $listing?->meta_title ?? '') }}" class="form-control rounded-4" maxlength="255">
            </div>
            <div class="mb-0">
                <label class="form-label small fw-semibold">{{ __('frontend.host_listing.meta_description') }}</label>
                <textarea name="meta_description" rows="2" class="form-control rounded-4" maxlength="500">{{ old('meta_description', $listing?->meta_description ?? '') }}</textarea>
            </div>
        </details>
    </div>
</div>

@if ($isEdit)
    <div class="nx-host-listing-section card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 p-lg-5">
            <h3 class="h6 fw-bold mb-3">{{ __('frontend.host_listing.verification_heading') }}</h3>
            <p class="small mb-2">
                <span class="badge rounded-pill px-3 py-2
                    {{ $listing->verification_status === 'verified' ? 'text-bg-success' :
                       ($listing->verification_status === 'rejected' ? 'text-bg-danger' : 'text-bg-warning text-dark') }}">
                    {{ ucfirst($listing->verification_status) }}
                </span>
            </p>
            @if ($listing->verification_notes)
                <p class="small text-muted mb-0">{{ $listing->verification_notes }}</p>
            @endif
        </div>
    </div>
@endif

<div class="card border-0 shadow-sm rounded-4 mb-5">
    <div class="card-body p-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div class="small text-muted">{{ __('frontend.host_listing.submit_hint') }}</div>
        <button type="submit" class="btn btn-dark btn-lg rounded-pill px-5 fw-bold" @if ($submitDisabled) disabled @endif>
            {{ $submitLabel }}
            <i class="bx bx-right-arrow-alt ms-1"></i>
        </button>
    </div>
</div>
