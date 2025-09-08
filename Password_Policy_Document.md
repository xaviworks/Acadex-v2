# Password Policy Document
## Web-Based Grading System (Acadex)

**Document Version:** 1.0  
**Date:** January 2025  
**System:** Web-Based Grading System (Laravel Framework)  
**Effective Date:** Immediately upon approval  
**Review Cycle:** Annual review and updates as needed

---

## Introduction

This Password Policy document establishes comprehensive guidelines for password management within the Web-Based Grading System (Acadex). The policy is designed to protect sensitive academic data, student records, and institutional information by implementing robust authentication controls. This document applies to all users of the system, including Administrators, Deans, Chairpersons, and Instructors.

The policy addresses the critical need for secure authentication mechanisms in an educational environment where sensitive student information, grades, and academic records must be protected from unauthorized access. By implementing these password requirements, the system ensures compliance with data protection regulations and maintains the integrity of academic operations.

---

## 1. Password Complexity Requirements

### 1.1 Minimum and Maximum Password Length

**Minimum Length:** 12 characters  
**Maximum Length:** 128 characters  
**Recommended Length:** 16-20 characters for optimal security

**Rationale:** The 12-character minimum provides sufficient entropy to resist brute-force attacks while remaining manageable for users. The maximum length accommodates passphrase strategies while preventing system performance issues.

### 1.2 Required Character Types

All passwords must contain a combination of the following character types:

- **Uppercase Letters (A-Z):** At least 1 character
- **Lowercase Letters (a-z):** At least 1 character  
- **Numbers (0-9):** At least 1 character
- **Special Characters:** At least 1 character from the following set:
  - `! @ # $ % ^ & * ( ) _ + - = { } [ ] | \ : " ; ' < > ? , . /`

**Character Distribution Requirements:**
- No more than 3 consecutive identical characters
- No more than 4 consecutive sequential characters (e.g., "1234", "abcd")
- No more than 3 consecutive keyboard patterns (e.g., "qwe", "asd")

### 1.3 Prohibited Passwords

The following password types are strictly prohibited:

**Common Weak Passwords:**
- "password", "123456", "qwerty", "admin", "welcome"
- "letmein", "monkey", "dragon", "master", "hello"
- Any variation of the system name: "acadex", "grading", "student"

**Personal Information-Based Passwords:**
- Username or email address
- User's first name, last name, or full name
- Student ID numbers or employee IDs
- Birth dates, phone numbers, or addresses
- Department names or course codes

**Pattern-Based Passwords:**
- Sequential numbers or letters (e.g., "123456789", "abcdefgh")
- Repeated characters (e.g., "aaaaaa", "111111")
- Keyboard patterns (e.g., "qwertyuiop", "asdfghjkl")

### 1.4 Password Strength Meters and Proactive Password Checkers

**Real-Time Password Strength Indicator:**
- Visual strength meter displayed during password creation
- Color-coded feedback: Red (weak), Yellow (moderate), Green (strong)
- Real-time validation of complexity requirements
- Immediate feedback on prohibited password patterns

**Password Strength Categories:**
- **Weak (Red):** Meets minimum requirements but easily guessable
- **Moderate (Yellow):** Meets all requirements with moderate complexity
- **Strong (Green):** Exceeds requirements with high complexity and uniqueness

**Proactive Password Validation:**
- Server-side validation of all password requirements
- Check against common password databases
- Validation of character distribution rules
- Prevention of common substitution patterns (e.g., "p@ssw0rd")

---

## 2. Password Expiration Policy

### 2.1 Frequency of Password Expiration

**Standard Users (Instructors, Chairpersons, Deans):**
- **Password Expiration:** 90 days from last password change
- **Grace Period:** 7 days after expiration date
- **Lockout Period:** Account locked after grace period expires

**Administrative Users:**
- **Password Expiration:** 60 days from last password change
- **Grace Period:** 3 days after expiration date
- **Lockout Period:** Immediate lockout after grace period

**System Administrators:**
- **Password Expiration:** 45 days from last password change
- **Grace Period:** 1 day after expiration date
- **Lockout Period:** Immediate lockout after grace period

### 2.2 Password Expiration Notifications

**Notification Schedule:**
- **First Warning:** 14 days before expiration
- **Second Warning:** 7 days before expiration
- **Final Warning:** 1 day before expiration
- **Expiration Notice:** On the day of expiration

**Notification Methods:**
- **Email Notifications:** Sent to user's registered email address
- **System Alerts:** In-system notifications upon login
- **Dashboard Warnings:** Prominent display on user dashboard
- **SMS Notifications:** Optional SMS alerts for critical accounts

**Notification Content:**
- Clear expiration date and time
- Instructions for password change
- Link to secure password change page
- Contact information for technical support

### 2.3 Forced Password Change Scenarios

**Initial Login Requirements:**
- **New User Accounts:** Must change password on first login
- **Temporary Passwords:** Must be changed within 24 hours
- **Admin-Created Accounts:** Require immediate password change
- **Imported User Accounts:** Force password change on first access

**Security Incident Triggers:**
- **Suspected Breach:** Immediate password change required
- **Multiple Failed Logins:** Account lockout with forced password reset
- **Unusual Access Patterns:** Security team may require password change
- **System Compromise:** All users may be required to change passwords

**Compliance Requirements:**
- **Audit Findings:** Password changes required based on security audits
- **Policy Updates:** Users notified of new password requirements
- **Regulatory Changes:** Compliance-driven password updates

---

## 3. Password Reuse and History

### 3.1 Password History Requirements

**Password History Retention:**
- **Standard Users:** Remember last 8 passwords
- **Administrative Users:** Remember last 12 passwords
- **System Administrators:** Remember last 15 passwords

**History Enforcement:**
- Users cannot reuse any password in the history list
- History applies to all password change methods
- Temporary passwords are included in history
- History is maintained across password resets

### 3.2 Rationale for Preventing Password Reuse

**Security Benefits:**
- **Reduced Risk:** Prevents attackers from using previously compromised passwords
- **Breach Mitigation:** Limits damage from credential stuffing attacks
- **Compliance:** Meets regulatory requirements for password security
- **Best Practices:** Aligns with industry security standards

**Attack Prevention:**
- **Credential Stuffing:** Prevents reuse of passwords from other breached systems
- **Brute Force:** Forces attackers to discover new passwords
- **Social Engineering:** Reduces effectiveness of password guessing
- **Insider Threats:** Limits damage from compromised accounts

### 3.3 Technical Implementation

**Database Storage:**
- **Encrypted Storage:** Password history stored using bcrypt hashing
- **Secure Transmission:** All password changes use HTTPS/TLS
- **Audit Logging:** All password changes logged with timestamps
- **Data Retention:** History maintained for specified retention period

**System Enforcement:**
- **Real-Time Validation:** Immediate check against password history
- **Server-Side Verification:** All password changes validated server-side
- **API Protection:** Password history checks in all authentication APIs
- **Middleware Integration:** Password validation in authentication middleware

**Administrative Controls:**
- **Override Capabilities:** System administrators can override history requirements
- **Emergency Procedures:** Temporary bypass procedures for critical situations
- **Audit Trail:** All overrides logged and reviewed
- **Compliance Monitoring:** Regular audits of password history enforcement

---

## 4. Password Recovery Procedures

### 4.1 Authentication Steps for Password Reset

**Multi-Factor Authentication Process:**
1. **Email Verification:** User must provide registered email address
2. **Security Questions:** Answer 2 out of 3 pre-configured security questions
3. **Account Verification:** System validates account status and permissions
4. **Rate Limiting:** Maximum 3 reset attempts per hour per email address

**Security Question Requirements:**
- **Question Types:** Personal questions not easily discoverable
- **Answer Complexity:** Minimum 3 characters, case-sensitive
- **Question Rotation:** Users must answer different questions each time
- **Answer Validation:** Partial matching with fuzzy logic for typos

**Account Status Verification:**
- **Active Account Check:** Verify account is not locked or suspended
- **Recent Activity:** Check for suspicious login patterns
- **Permission Validation:** Ensure user has password reset privileges
- **Department Verification:** Confirm user belongs to authorized department

### 4.2 Password Reset Restrictions

**Time-Based Restrictions:**
- **Reset Token Expiration:** 30 minutes from token generation
- **Session Timeout:** 15 minutes of inactivity during reset process
- **Daily Limits:** Maximum 5 password reset attempts per day
- **Weekly Limits:** Maximum 10 password reset attempts per week

**Device and Location Restrictions:**
- **IP Address Validation:** Reset only from previously used IP addresses
- **Geographic Restrictions:** Block resets from unusual locations
- **Device Fingerprinting:** Track and validate device characteristics
- **VPN Detection:** Flag and require additional verification for VPN connections

**Account-Specific Restrictions:**
- **Administrative Accounts:** Require additional approval for password resets
- **Recently Created Accounts:** Enhanced verification for new accounts
- **Suspended Accounts:** No password reset allowed for suspended accounts
- **Compromised Accounts:** Special procedures for suspected compromised accounts

### 4.3 Temporary Password Handling

**Temporary Password Generation:**
- **Complexity Requirements:** Meet all standard password complexity rules
- **Random Generation:** Cryptographically secure random password generation
- **Length:** 16 characters with mixed character types
- **Readability:** Avoid confusing characters (0/O, 1/l, etc.)

**Temporary Password Security:**
- **One-Time Use:** Temporary passwords can only be used once
- **Immediate Expiration:** Expire after first successful login
- **Forced Change:** Users must change password immediately upon login
- **No History:** Temporary passwords are not stored in password history

**Communication Security:**
- **Secure Delivery:** Temporary passwords sent via encrypted email
- **No Plain Text:** Passwords never sent in plain text format
- **Time-Limited Access:** Email links expire after 30 minutes
- **Audit Logging:** All temporary password generation logged

**User Experience Considerations:**
- **Clear Instructions:** Step-by-step guidance for password reset process
- **Progress Indicators:** Visual feedback during reset process
- **Error Handling:** Clear error messages for failed attempts
- **Support Contact:** Easy access to technical support during process

---

## 5. Compliance and Enforcement

### 5.1 User Compliance Expectations

**Mandatory Requirements:**
- All users must comply with password policy requirements
- Password changes must be completed within specified timeframes
- Users must not share passwords or credentials with others
- Users must report suspected security incidents immediately

**Best Practices:**
- Use unique passwords for each system account
- Consider using password managers for secure storage
- Regularly review and update security questions
- Enable multi-factor authentication when available

**Prohibited Activities:**
- Writing down passwords in easily accessible locations
- Sharing passwords via email, chat, or other unsecured methods
- Using the same password for multiple systems
- Attempting to bypass password security measures

### 5.2 Consequences of Non-Compliance

**Progressive Disciplinary Actions:**
- **First Violation:** Warning and mandatory security training
- **Second Violation:** Temporary account suspension (24-48 hours)
- **Third Violation:** Extended account suspension (1 week)
- **Repeated Violations:** Permanent account termination

**Security Incident Consequences:**
- **Immediate Suspension:** Account suspended during investigation
- **Mandatory Password Change:** Forced password reset for all accounts
- **Security Review:** Comprehensive account and access review
- **Disciplinary Action:** Referral to appropriate administrative authority

**Administrative Actions:**
- **Account Monitoring:** Enhanced monitoring of non-compliant users
- **Access Restrictions:** Limited access to system features
- **Training Requirements:** Mandatory security awareness training
- **Performance Impact:** Non-compliance may affect performance evaluations

### 5.3 Monitoring and Reporting

**Automated Monitoring:**
- **Password Compliance Checks:** Automated verification of password requirements
- **Expiration Tracking:** Real-time monitoring of password expiration dates
- **Violation Detection:** Automated detection of policy violations
- **Alert Generation:** Immediate alerts for security incidents

**Regular Audits:**
- **Monthly Reviews:** Review of password policy compliance
- **Quarterly Assessments:** Comprehensive security assessments
- **Annual Evaluations:** Full policy effectiveness evaluation
- **Compliance Reporting:** Regular reports to management and stakeholders

---

## 6. Conclusion

This Password Policy establishes a comprehensive framework for secure password management within the Web-Based Grading System. The policy balances security requirements with usability considerations, ensuring that sensitive academic data remains protected while maintaining efficient system operations.

All users are expected to fully comply with these password requirements. The policy will be regularly reviewed and updated to address emerging security threats and technological advancements. Users should familiarize themselves with these requirements and seek clarification from the IT security team if needed.

The successful implementation of this password policy requires cooperation from all system users, administrators, and stakeholders. By following these guidelines, we can maintain the security and integrity of our academic management system while protecting the sensitive information entrusted to our care.

**For questions or concerns regarding this policy, please contact the IT Security Team at security@acadex.edu**

---

**Document Control:**
- **Version:** 1.0
- **Last Updated:** January 2025
- **Next Review:** July 2025
- **Approved By:** [IT Security Team/Management]
- **Distribution:** All system users and administrators 