@extends('layouts.app')

@section('title', 'FAQ')

@section('content')
    <div style="max-width: 800px; margin: 0 auto;">
        <h2 style="text-align: center; margin-bottom: 2rem;">❓ Frequently Asked Questions</h2>

        <div class="faq-list" style="display: flex; flex-direction: column; gap: 1rem;">
            <details class="faq-item"
                style="background: linear-gradient(145deg, #ffffff, #f8fafc); border-radius: 1rem; padding: 1.25rem 1.5rem; border: 1px solid rgba(20, 184, 166, 0.1); box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                <summary
                    style="cursor: pointer; font-weight: 600; color: #0f172a; font-size: 1.1rem; list-style: none; display: flex; align-items: center; gap: 0.75rem;">
                    <span
                        style="background: linear-gradient(135deg, #14b8a6, #0d9488); color: white; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">1</span>
                    How do I register?
                </summary>
                <p style="margin-top: 1rem; color: #64748b; line-height: 1.6;">Click on <strong>Register</strong> at the top
                    of the page and fill in your details. You can choose to register as a customer or a restaurant owner.
                </p>
            </details>

            <details class="faq-item"
                style="background: linear-gradient(145deg, #ffffff, #f8fafc); border-radius: 1rem; padding: 1.25rem 1.5rem; border: 1px solid rgba(20, 184, 166, 0.1); box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                <summary
                    style="cursor: pointer; font-weight: 600; color: #0f172a; font-size: 1.1rem; list-style: none; display: flex; align-items: center; gap: 0.75rem;">
                    <span
                        style="background: linear-gradient(135deg, #14b8a6, #0d9488); color: white; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">2</span>
                    How do I book a restaurant?
                </summary>
                <p style="margin-top: 1rem; color: #64748b; line-height: 1.6;">Browse our restaurant listings, select your
                    preferred option, and click <strong>Make Reservation</strong>. Fill in the date, time, and number of
                    guests to complete your booking.</p>
            </details>

            <details class="faq-item"
                style="background: linear-gradient(145deg, #ffffff, #f8fafc); border-radius: 1rem; padding: 1.25rem 1.5rem; border: 1px solid rgba(20, 184, 166, 0.1); box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                <summary
                    style="cursor: pointer; font-weight: 600; color: #0f172a; font-size: 1.1rem; list-style: none; display: flex; align-items: center; gap: 0.75rem;">
                    <span
                        style="background: linear-gradient(135deg, #14b8a6, #0d9488); color: white; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">3</span>
                    How do I delete my account?
                </summary>
                <p style="margin-top: 1rem; color: #64748b; line-height: 1.6;">Go to your profile page and scroll to the
                    bottom. Click <strong>Delete Account</strong> and confirm your decision. This action is permanent.</p>
            </details>

            <details class="faq-item"
                style="background: linear-gradient(145deg, #ffffff, #f8fafc); border-radius: 1rem; padding: 1.25rem 1.5rem; border: 1px solid rgba(20, 184, 166, 0.1); box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                <summary
                    style="cursor: pointer; font-weight: 600; color: #0f172a; font-size: 1.1rem; list-style: none; display: flex; align-items: center; gap: 0.75rem;">
                    <span
                        style="background: linear-gradient(135deg, #14b8a6, #0d9488); color: white; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">4</span>
                    Why has my reservation been cancelled?
                </summary>
                <p style="margin-top: 1rem; color: #64748b; line-height: 1.6;">Restaurant owners have the right to manage
                    their reservations. Cancellations may occur due to capacity issues, special events, or other operational
                    reasons.</p>
            </details>

            <details class="faq-item"
                style="background: linear-gradient(145deg, #ffffff, #f8fafc); border-radius: 1rem; padding: 1.25rem 1.5rem; border: 1px solid rgba(20, 184, 166, 0.1); box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                <summary
                    style="cursor: pointer; font-weight: 600; color: #0f172a; font-size: 1.1rem; list-style: none; display: flex; align-items: center; gap: 0.75rem;">
                    <span
                        style="background: linear-gradient(135deg, #14b8a6, #0d9488); color: white; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">5</span>
                    Is my name visible to other users?
                </summary>
                <p style="margin-top: 1rem; color: #64748b; line-height: 1.6;">Yes, users who visit your profile can see
                    your name and surname. Restaurant owners can see this information on reservations. If privacy is a
                    concern, you may use a pseudonym.</p>
            </details>

            <details class="faq-item"
                style="background: linear-gradient(145deg, #ffffff, #f8fafc); border-radius: 1rem; padding: 1.25rem 1.5rem; border: 1px solid rgba(20, 184, 166, 0.1); box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                <summary
                    style="cursor: pointer; font-weight: 600; color: #0f172a; font-size: 1.1rem; list-style: none; display: flex; align-items: center; gap: 0.75rem;">
                    <span
                        style="background: linear-gradient(135deg, #14b8a6, #0d9488); color: white; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">6</span>
                    Why can't I reply to owner responses?
                </summary>
                <p style="margin-top: 1rem; color: #64748b; line-height: 1.6;">To maintain a clean and readable review
                    section, we limit back-and-forth exchanges. This helps keep conversations constructive and focused.</p>
            </details>
        </div>

        <div
            style="text-align: center; margin-top: 3rem; padding: 2rem; background: linear-gradient(145deg, #f0fdfa, #ccfbf1); border-radius: 1rem;">
            <h3 style="margin-bottom: 0.5rem; color: #0f172a;">Still have questions?</h3>
            <p style="color: #64748b; margin-bottom: 1rem;">We're here to help!</p>
            <a href="mailto:support@eatzy.com" class="button">Contact Support</a>
        </div>
    </div>
@endsection