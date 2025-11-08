<?php

/**
 * Seed boilerplate policy templates for CMMC ML2 compliance
 *
 * These policies cover all 14 domains from NIST 800-171 required for CMMC Level 2
 */

return function($db) {
    $policies = [
        // 1. Access Control Policy
        [
            'title' => 'Access Control Policy',
            'category' => 'Access Control',
            'frameworks' => 'CMMC,NIST800171',
            'description' => 'Defines requirements for limiting system access to authorized users, processes, and devices.',
            'version' => '1.0',
            'content' => "PURPOSE:
This policy establishes the requirements for controlling access to [ORGANIZATION NAME] information systems and data.

SCOPE:
This policy applies to all employees, contractors, vendors, and third parties who access [ORGANIZATION NAME] information systems.

POLICY STATEMENTS:

1. Access Authorization
   - All system access must be authorized by management before being granted
   - Access rights are based on the principle of least privilege
   - Access is granted only for legitimate business purposes
   - Default passwords must be changed upon first login

2. Account Management
   - User accounts will be created, modified, and terminated based on personnel status changes
   - Shared accounts are prohibited except where technically necessary and documented
   - Privileged accounts will be limited to authorized personnel only
   - Account reviews will be conducted at least annually

3. Access Control Mechanisms
   - Multi-factor authentication (MFA) is required for remote access and privileged accounts
   - Session timeout will be set to [15] minutes of inactivity
   - Concurrent session limits will be enforced where applicable
   - Failed login attempts will be limited to [5] attempts before account lockout

4. Remote Access
   - Remote access requires MFA and encrypted connections (VPN)
   - Remote access sessions must use encrypted protocols
   - Personal devices used for remote access must meet security requirements

5. Account Termination
   - Access will be revoked immediately upon termination or role change
   - Exit procedures include credential surrender and access verification

RESPONSIBILITIES:
- IT Department: Implement and maintain access controls
- Managers: Authorize access and notify IT of personnel changes
- Users: Protect credentials and report unauthorized access

ENFORCEMENT:
Violations may result in disciplinary action up to and including termination.

REVIEW:
This policy will be reviewed annually and updated as needed.",
        ],

        // 2. Awareness and Training Policy
        [
            'title' => 'Security Awareness and Training Policy',
            'category' => 'Awareness and Training',
            'frameworks' => 'CMMC,NIST800171,HIPAA,PCI-DSS',
            'description' => 'Establishes requirements for security awareness training and role-based security training programs.',
            'version' => '1.0',
            'content' => "PURPOSE:
To ensure all personnel are aware of security risks and trained on security responsibilities.

SCOPE:
All employees, contractors, and third parties with access to [ORGANIZATION NAME] systems.

POLICY STATEMENTS:

1. Security Awareness Training
   - All personnel must complete security awareness training upon hire
   - Annual refresher training is required for all personnel
   - Training topics include: phishing, password security, physical security, incident reporting

2. Role-Based Security Training
   - Personnel with security responsibilities receive specialized training
   - System administrators receive training on secure configuration and management
   - Developers receive secure coding training
   - Training is documented and tracked

3. Training Content
   - Identifying and reporting security incidents
   - Protecting CUI and sensitive information
   - Social engineering and phishing awareness
   - Password and authentication best practices
   - Physical security requirements
   - Mobile device and remote work security

4. Phishing Testing
   - Simulated phishing campaigns conducted quarterly
   - Results tracked and additional training provided as needed

5. Training Records
   - Training completion is documented and maintained
   - Records retained for [3] years minimum

RESPONSIBILITIES:
- Security Team: Develop and deliver training programs
- HR: Track training completion
- Managers: Ensure team members complete required training

REVIEW:
Training content reviewed and updated annually.",
        ],

        // 3. Audit and Accountability Policy
        [
            'title' => 'Audit and Accountability Policy',
            'category' => 'Audit and Accountability',
            'frameworks' => 'CMMC,NIST800171,HIPAA,SOC2',
            'description' => 'Defines requirements for system auditing, logging, and review of audit records.',
            'version' => '1.0',
            'content' => "PURPOSE:
To establish requirements for creating, protecting, and retaining audit records.

SCOPE:
All information systems processing, storing, or transmitting [ORGANIZATION NAME] data.

POLICY STATEMENTS:

1. Audit Logging
   - Systems must log security-relevant events including:
     * Successful and failed login attempts
     * Account management activities
     * Privileged operations
     * Access to sensitive data
     * Security configuration changes
     * System startup and shutdown

2. Audit Record Content
   - Logs must include: date/time, user ID, event type, success/failure, source/destination
   - Logs must be in a format suitable for analysis
   - Time synchronization across all systems required (NTP)

3. Log Protection
   - Audit logs are protected from unauthorized access, modification, and deletion
   - Log data encrypted in transit and at rest
   - Access to logs restricted to authorized personnel
   - Logs backed up regularly

4. Log Retention
   - Logs retained for minimum [1 year]
   - Extended retention for systems processing CUI: [3 years]
   - Logs stored in centralized logging system when possible

5. Log Review and Analysis
   - Logs reviewed [weekly] for suspicious activity
   - Automated alerts configured for critical events
   - Security Information and Event Management (SIEM) used where available
   - Review activities documented

6. Audit Response
   - Suspicious activities investigated promptly
   - Incident response procedures invoked when appropriate
   - Findings documented and addressed

RESPONSIBILITIES:
- IT Department: Configure and maintain logging systems
- Security Team: Review logs and investigate anomalies
- System Owners: Ensure systems generate adequate logs

REVIEW:
Policy reviewed annually.",
        ],

        // 4. Configuration Management Policy
        [
            'title' => 'Configuration Management Policy',
            'category' => 'Configuration Management',
            'frameworks' => 'CMMC,NIST800171',
            'description' => 'Establishes requirements for baseline configurations, change control, and security configuration settings.',
            'version' => '1.0',
            'content' => "PURPOSE:
To ensure information systems are configured securely and changes are controlled.

SCOPE:
All information systems and network devices owned or managed by [ORGANIZATION NAME].

POLICY STATEMENTS:

1. Baseline Configurations
   - Secure baseline configurations established for all systems
   - Baselines documented and maintained for servers, workstations, and network devices
   - Security configuration checklists used (CIS Benchmarks, DISA STIGs)
   - Default passwords changed before deployment
   - Unnecessary services and features disabled

2. Configuration Change Control
   - All configuration changes must be approved before implementation
   - Change requests documented with business justification
   - Emergency changes documented retroactively within [24 hours]
   - Testing required in non-production environment when possible
   - Rollback procedures documented for all changes

3. Least Functionality
   - Only essential software and services installed
   - Unauthorized software prohibited
   - Software inventory maintained and reviewed quarterly
   - Application whitelisting implemented where feasible

4. System Hardening
   - Operating systems hardened per security benchmarks
   - Unused ports and services disabled
   - Security patches applied within [30] days for critical vulnerabilities
   - Anti-malware software installed and updated on all endpoints

5. Configuration Monitoring
   - Automated tools monitor for configuration drift
   - Unauthorized changes detected and remediated
   - Configuration audits conducted quarterly

6. User-Installed Software
   - Users prohibited from installing unauthorized software
   - Software requests submitted through IT ticketing system
   - Approved software list maintained

RESPONSIBILITIES:
- IT Department: Maintain baseline configurations and process changes
- Change Advisory Board: Review and approve changes
- System Owners: Ensure systems comply with baselines

REVIEW:
Baselines reviewed annually and after significant changes.",
        ],

        // 5. Identification and Authentication Policy
        [
            'title' => 'Identification and Authentication Policy',
            'category' => 'Identification and Authentication',
            'frameworks' => 'CMMC,NIST800171,HIPAA,PCI-DSS',
            'description' => 'Defines requirements for user identification, authentication mechanisms, and credential management.',
            'version' => '1.0',
            'content' => "PURPOSE:
To establish requirements for identifying and authenticating users and devices.

SCOPE:
All systems, applications, and network devices.

POLICY STATEMENTS:

1. User Identification
   - Each user assigned unique identifier (username)
   - Shared accounts prohibited except where documented and approved
   - Generic accounts (guest, admin) disabled or renamed
   - User IDs not reused for [2 years] after deactivation

2. Password Requirements
   - Minimum length: [14] characters
   - Complexity: Must include uppercase, lowercase, numbers, and special characters
   - Password expiration: [90] days (or use passwordless/MFA)
   - Password history: Prevent reuse of last [12] passwords
   - No common passwords (checked against breach databases)
   - Passwords not displayed when entered

3. Multi-Factor Authentication (MFA)
   - Required for:
     * All remote access (VPN, cloud services)
     * Privileged/administrative accounts
     * Access to systems containing CUI
   - MFA methods: Authenticator apps, hardware tokens, biometrics
   - SMS-based MFA avoided when stronger methods available

4. Account Lockout
   - Accounts locked after [5] failed login attempts
   - Lockout duration: [30] minutes or until administrator unlock
   - Lockout notifications sent to security team

5. Session Management
   - Idle timeout: [15] minutes of inactivity
   - Re-authentication required after timeout
   - Concurrent session limits enforced
   - Sessions terminated upon logout

6. Service Accounts
   - Service account passwords minimum [24] characters
   - Changed when personnel with knowledge depart
   - Documented and reviewed annually
   - Privileged access managed and monitored

7. Password Storage
   - Passwords stored using approved cryptographic hash (bcrypt, PBKDF2)
   - Passwords never stored in plaintext
   - Password transmission encrypted (HTTPS, SSH)

RESPONSIBILITIES:
- IT Department: Configure authentication systems and enforce requirements
- Users: Create strong passwords, protect credentials, use MFA
- Security Team: Monitor authentication failures and investigate anomalies

REVIEW:
Policy reviewed annually.",
        ],

        // 6. Incident Response Policy
        [
            'title' => 'Incident Response Policy',
            'category' => 'Incident Response',
            'frameworks' => 'CMMC,NIST800171,HIPAA,SOC2,ISO27001',
            'description' => 'Establishes procedures for detecting, responding to, and recovering from security incidents.',
            'version' => '1.0',
            'content' => "PURPOSE:
To ensure security incidents are identified, reported, and handled appropriately.

SCOPE:
All personnel and information systems.

POLICY STATEMENTS:

1. Incident Response Capability
   - Incident Response Team (IRT) established and trained
   - Incident response procedures documented and tested
   - Contact information for IRT members maintained
   - 24/7 incident reporting mechanism available

2. Incident Detection
   - Security monitoring tools deployed (SIEM, IDS/IPS, EDR)
   - Automated alerts configured for suspicious activities
   - Users trained to recognize and report security incidents
   - Indicators of Compromise (IOCs) monitored

3. Incident Reporting
   - All suspected incidents reported immediately to: [security@organization.com]
   - Report includes: what happened, when, who discovered, systems affected
   - No penalty for good-faith reporting
   - External reporting requirements followed (breach notifications, law enforcement)

4. Incident Classification
   - Severity levels: Critical, High, Medium, Low
   - Critical: CUI breach, ransomware, system compromise
   - Response times based on severity (Critical: immediate, High: 4 hours, etc.)

5. Incident Response Process
   a. Preparation: Tools ready, team trained, procedures current
   b. Detection & Analysis: Determine scope and severity
   c. Containment: Short-term (isolate), long-term (rebuild)
   d. Eradication: Remove threat, close vulnerabilities
   e. Recovery: Restore systems, verify operation
   f. Post-Incident: Lessons learned, update procedures

6. Evidence Preservation
   - Chain of custody maintained for forensic evidence
   - System images and logs preserved
   - Legal and law enforcement requirements followed

7. Communication
   - Internal stakeholders notified per communication plan
   - External notifications (customers, regulators) as required
   - Media inquiries handled by designated spokesperson only

8. Post-Incident Activities
   - Post-incident review conducted within [30] days
   - Root cause analysis performed
   - Corrective actions identified and implemented
   - Incident documentation retained per retention schedule

9. Testing and Training
   - Incident response plan tested annually (tabletop exercise)
   - IRT members receive specialized training
   - Procedures updated based on test results and actual incidents

RESPONSIBILITIES:
- All Personnel: Report suspected incidents
- Incident Response Team: Respond to and manage incidents
- Management: Provide resources and support
- Legal/Compliance: Ensure regulatory requirements met

REVIEW:
Policy and procedures reviewed annually and after significant incidents.",
        ],

        // 7. Maintenance Policy
        [
            'title' => 'System Maintenance Policy',
            'category' => 'Maintenance',
            'frameworks' => 'CMMC,NIST800171',
            'description' => 'Defines requirements for performing system maintenance and controlling maintenance tools.',
            'version' => '1.0',
            'content' => "PURPOSE:
To ensure system maintenance is performed securely and does not introduce vulnerabilities.

SCOPE:
All information systems and maintenance activities.

POLICY STATEMENTS:

1. Scheduled Maintenance
   - Maintenance schedules documented and communicated
   - Maintenance windows established for production systems
   - Emergency maintenance procedures defined
   - Maintenance activities logged and tracked

2. Maintenance Personnel
   - Only authorized personnel perform maintenance
   - Third-party maintenance providers vetted and supervised
   - Background checks completed for maintenance personnel
   - Access limited to time/scope of maintenance activity

3. Maintenance Tools
   - Approved tools documented in tool inventory
   - Tools inspected for malicious code before use
   - Remote maintenance tools strictly controlled
   - Tool access logged and monitored
   - Tools removed from systems after use

4. Remote Maintenance
   - Remote maintenance requires multi-factor authentication
   - Remote sessions encrypted (VPN, SSH, encrypted RDP)
   - Remote maintenance sessions monitored and logged
   - Remote access terminated immediately after completion
   - Approval required before remote maintenance

5. Maintenance Controls
   - CUI removed or protected during maintenance
   - Systems sanitized before offsite maintenance
   - Removable media encrypted if leaving facility
   - Maintenance performed in secure environment when possible

6. Documentation
   - Maintenance activities documented including:
     * Date, time, duration
     * Personnel involved
     * Systems affected
     * Work performed
     * Parts replaced
   - Records retained per retention policy

7. Testing After Maintenance
   - Systems tested after maintenance before return to production
   - Security controls verified operational
   - Unauthorized changes detected and remediated

RESPONSIBILITIES:
- IT Department: Perform and document maintenance
- System Owners: Approve maintenance schedules
- Security Team: Monitor maintenance activities

REVIEW:
Policy reviewed annually.",
        ],

        // 8. Media Protection Policy
        [
            'title' => 'Media Protection Policy',
            'category' => 'Media Protection',
            'frameworks' => 'CMMC,NIST800171,HIPAA,PCI-DSS',
            'description' => 'Establishes requirements for protecting, sanitizing, and disposing of physical and digital media.',
            'version' => '1.0',
            'content' => "PURPOSE:
To protect information stored on physical and digital media throughout its lifecycle.

SCOPE:
All media containing organizational information (hard drives, SSDs, USB drives, backup tapes, mobile devices, paper documents).

POLICY STATEMENTS:

1. Media Identification and Marking
   - Media containing CUI clearly labeled with classification
   - Labels include data owner and destruction date
   - Media inventory maintained for CUI storage media

2. Media Storage
   - CUI media stored in locked, access-controlled locations
   - Backup media stored offsite in secure facility
   - Media access restricted to authorized personnel
   - Media checkout/check-in logged

3. Media Transport
   - CUI media encrypted before transport
   - Approved courier services used for media transport
   - Chain of custody maintained during transport
   - Media shipped in tamper-evident packaging
   - Tracking numbers recorded for shipments

4. Media Sanitization
   - Media sanitized before reuse or disposal using approved methods:
     * Overwriting: DOD 5220.22-M (3 passes minimum)
     * Degaussing: For magnetic media
     * Physical destruction: Shredding, pulverizing, incinerating
   - Sanitization appropriate to classification level
   - Encrypted media: Cryptographic erasure (destroy keys)
   - Sanitization documented with date, method, personnel

5. Media Disposal
   - Disposal methods:
     * Hard drives: Destruction by certified vendor with certificate
     * Paper: Cross-cut shredding or incineration
     * Optical media: Physical destruction
     * Mobile devices: Factory reset + physical destruction
   - Certificate of destruction obtained and retained
   - Media never disposed in regular trash

6. Portable Media
   - Removable media (USB drives) must be encrypted
   - Personal removable media prohibited on corporate systems
   - Approved media tracked and inventoried
   - Antivirus scanning before use

7. Mobile Devices
   - Organizational data on mobile devices encrypted
   - Remote wipe capability enabled
   - Lost/stolen devices reported immediately
   - Devices sanitized before reissue or disposal

RESPONSIBILITIES:
- IT Department: Sanitize and dispose of media securely
- All Personnel: Protect media and report lost/stolen media
- Security Team: Audit media protection controls

REVIEW:
Policy reviewed annually.",
        ],

        // 9. Personnel Security Policy
        [
            'title' => 'Personnel Security Policy',
            'category' => 'Personnel Security',
            'frameworks' => 'CMMC,NIST800171,HIPAA,SOC2,ISO27001',
            'description' => 'Defines requirements for personnel screening, termination procedures, and security responsibilities.',
            'version' => '1.0',
            'content' => "PURPOSE:
To ensure personnel are trustworthy and understand security responsibilities.

SCOPE:
All employees, contractors, and third parties with access to organizational systems or facilities.

POLICY STATEMENTS:

1. Position Categorization
   - Positions categorized by risk level and access to sensitive data
   - Higher risk positions require enhanced screening
   - Position risk reviewed annually

2. Personnel Screening
   - Background checks completed before granting access:
     * Criminal history check
     * Employment verification
     * Education verification (for certain positions)
     * Credit check (for positions with financial access)
   - Screening repeated every [5] years for positions with CUI access
   - Adverse findings reviewed by HR and management

3. Onboarding
   - New personnel sign confidentiality agreements
   - Acceptable use policy acknowledged
   - Security awareness training completed before access granted
   - Roles and responsibilities documented

4. Security Roles and Responsibilities
   - Job descriptions include security responsibilities
   - Personnel understand consequences of security violations
   - Managers ensure team members meet security requirements

5. Personnel Sanctions
   - Violations of security policies result in disciplinary action
   - Sanctions may include:
     * Verbal warning
     * Written warning
     * Suspension
     * Termination
     * Legal action
   - Sanctions documented in personnel file

6. Personnel Transfer
   - Access rights reviewed and modified when roles change
   - Security responsibilities updated
   - New position may require additional screening or training

7. Personnel Termination
   - Termination procedures include:
     * Access revocation (systems, facilities, applications)
     * Credential surrender (badges, tokens, keys, devices)
     * Exit interview covering security obligations
     * Confidentiality agreements remain in effect
   - Access terminated on or before last day of employment
   - Manager notifies IT of termination immediately

8. Third Parties
   - Third-party personnel subject to same requirements
   - Contracts include security requirements
   - Access limited to minimum necessary
   - Third-party access reviewed quarterly

RESPONSIBILITIES:
- HR: Conduct screening, manage onboarding/termination
- Managers: Notify HR of personnel changes, enforce policy
- IT: Provision and revoke access
- Employees: Understand and comply with responsibilities

REVIEW:
Policy reviewed annually.",
        ],

        // 10. Physical Protection Policy
        [
            'title' => 'Physical Protection Policy',
            'category' => 'Physical Protection',
            'frameworks' => 'CMMC,NIST800171,HIPAA,SOC2,ISO27001',
            'description' => 'Establishes requirements for physical access controls, monitoring, and visitor management.',
            'version' => '1.0',
            'content' => "PURPOSE:
To protect facilities, systems, and information from physical threats.

SCOPE:
All organizational facilities and areas containing information systems or sensitive data.

POLICY STATEMENTS:

1. Physical Access Control
   - Facilities secured with physical access controls (locks, badge readers, biometrics)
   - Access granted based on business need
   - Visitor access restricted and escorted
   - After-hours access restricted and logged
   - Keys and access cards tracked and inventoried

2. Facility Access Authorization
   - Access authorization documented and approved by management
   - Access lists reviewed quarterly
   - Terminated personnel access revoked immediately
   - Lost/stolen access credentials reported and revoked immediately

3. Physical Access Monitoring
   - Access logs maintained for [90] days minimum
   - Video surveillance in sensitive areas (server rooms, entrances)
   - Video recordings retained for [30] days minimum
   - Access anomalies investigated

4. Visitor Control
   - Visitors sign in/out and present valid ID
   - Visitors issued temporary badges
   - Visitors escorted at all times in restricted areas
   - Visitor logs maintained for [1] year

5. Physical Security Devices
   - Intrusion detection/alarm systems installed
   - Alarms monitored and responded to
   - Systems tested quarterly
   - Fire detection and suppression systems maintained

6. Secure Areas
   - Server rooms and data centers designated as secure areas
   - Secure areas have enhanced controls:
     * Dual authentication (badge + PIN or badge + biometric)
     * Mantrap entry
     * Environmental controls (temperature, humidity)
     * Fire suppression
     * Restricted access list
   - Secure areas inspected annually

7. Work in Secure Areas
   - Equipment in secure areas inventoried
   - Photography prohibited without authorization
   - Personal devices restricted
   - Unattended systems automatically locked

8. Delivery and Loading Areas
   - Deliveries received in designated areas only
   - Deliveries inspected before acceptance
   - Loading areas separated from secure areas

9. Asset Protection
   - Equipment secured to prevent theft (cable locks)
   - Portable devices encrypted
   - High-value equipment inventoried with serial numbers
   - Equipment movement logs maintained

10. Alternative Work Sites
    - Remote work locations meet minimum security requirements
    - Sensitive discussions held in private areas
    - Screens positioned to prevent unauthorized viewing
    - Clear desk policy enforced

RESPONSIBILITIES:
- Facilities: Maintain physical security controls
- Security Team: Monitor access and investigate incidents
- All Personnel: Challenge unescorted visitors, report security concerns

REVIEW:
Policy reviewed annually.",
        ],

        // 11. Risk Assessment Policy
        [
            'title' => 'Risk Assessment Policy',
            'category' => 'Risk Assessment',
            'frameworks' => 'CMMC,NIST800171,HIPAA,SOC2,ISO27001',
            'description' => 'Defines requirements for identifying, analyzing, and responding to risks.',
            'version' => '1.0',
            'content' => "PURPOSE:
To identify, assess, and mitigate security risks to organizational operations and assets.

SCOPE:
All information systems and business processes.

POLICY STATEMENTS:

1. Risk Assessment Process
   - Risk assessments conducted:
     * Annually for all systems
     * Before system changes or new system deployment
     * After significant security incidents
     * When new threats or vulnerabilities identified
   - Risk assessment methodology documented (NIST SP 800-30 or similar)

2. Threat Identification
   - Internal and external threats identified:
     * Natural disasters
     * Cyber attacks
     * Insider threats
     * Supply chain risks
     * Human error
   - Threat intelligence sources consulted

3. Vulnerability Assessment
   - Vulnerability scanning conducted monthly
   - Penetration testing conducted annually
   - Vulnerabilities prioritized by severity (CVSS scores)
   - False positives investigated and documented

4. Risk Analysis
   - Likelihood and impact determined for each risk:
     * Likelihood: High, Medium, Low
     * Impact: High, Medium, Low
   - Risk level = Likelihood x Impact
   - CUI risks analyzed separately with higher scrutiny

5. Risk Response
   - Response strategies:
     * Mitigate: Implement controls to reduce risk
     * Accept: Document acceptance by management
     * Transfer: Insurance, outsourcing with contractual protections
     * Avoid: Eliminate the risk source
   - Critical and High risks addressed within [30] days
   - Medium risks addressed within [90] days
   - Low risks monitored

6. Risk Register
   - All identified risks documented in risk register
   - Register includes:
     * Risk description
     * Likelihood and impact
     * Risk level
     * Response strategy
     * Owner
     * Status
   - Register reviewed quarterly

7. Supply Chain Risk
   - Critical suppliers identified and assessed
   - Security requirements included in contracts
   - Supplier security posture reviewed annually
   - Alternative suppliers identified for critical services

8. Risk Monitoring
   - Risk environment monitored for changes
   - New threats and vulnerabilities tracked
   - Risk register updated as risks change
   - Residual risks reported to management

9. Risk Communication
   - Risk assessment results reported to senior management
   - Significant risks escalated immediately
   - Risk decisions documented
   - Stakeholders informed of risk-related changes

RESPONSIBILITIES:
- Security Team: Conduct risk assessments, maintain risk register
- System Owners: Identify risks for owned systems, implement mitigations
- Management: Make risk decisions, provide resources for risk mitigation

REVIEW:
Policy reviewed annually.",
        ],

        // 12. Security Assessment Policy
        [
            'title' => 'Security Assessment Policy',
            'category' => 'Security Assessment',
            'frameworks' => 'CMMC,NIST800171,SOC2,ISO27001',
            'description' => 'Establishes requirements for assessing security controls, remediation, and continuous monitoring.',
            'version' => '1.0',
            'content' => "PURPOSE:
To verify security controls are implemented correctly, operating as intended, and producing desired outcomes.

SCOPE:
All information systems and security controls.

POLICY STATEMENTS:

1. Security Control Assessments
   - Security controls assessed:
     * Annually for all systems
     * After significant changes
     * After security incidents
   - Assessment conducted by qualified personnel
   - Independent assessors used for critical systems

2. Assessment Scope
   - Assessments include:
     * Technical controls (firewalls, encryption, access controls)
     * Operational controls (procedures, training, incident response)
     * Management controls (policies, risk assessments)
   - CUI systems assessed against NIST 800-171 requirements

3. Assessment Methods
   - Methods used:
     * Interviews with personnel
     * Document review
     * Technical testing (vulnerability scans, penetration tests)
     * Observation of procedures
     * Automated compliance scanning

4. Vulnerability Scanning
   - Authenticated vulnerability scans monthly
   - Unauthenticated scans quarterly
   - Critical vulnerabilities remediated within [15] days
   - High vulnerabilities remediated within [30] days
   - Scan results tracked and trended

5. Penetration Testing
   - External penetration testing annually
   - Internal penetration testing annually
   - Tests conducted by qualified third parties
   - Findings documented and remediated
   - Retest after remediation

6. Remediation
   - Findings documented with:
     * Severity rating
     * Affected systems
     * Remediation recommendation
     * Owner
     * Due date
   - Remediation plans created for all findings
   - Progress tracked in ticketing system
   - Critical findings escalated to management

7. Plan of Action and Milestones (POA&M)
   - POA&M maintained for all open findings
   - Updated monthly with current status
   - Overdue items escalated to management
   - Accepted risks documented with management approval

8. Continuous Monitoring
   - Ongoing monitoring of security posture:
     * Automated compliance checks
     * Log analysis
     * Threat intelligence
     * Change detection
   - Dashboards and metrics reviewed weekly
   - Trends analyzed for early warning signs

9. Assessment Reporting
   - Assessment results documented in formal reports
   - Reports include:
     * Executive summary
     * Findings and recommendations
     * Risk ratings
     * Remediation timelines
   - Reports provided to senior management and system owners

RESPONSIBILITIES:
- Security Team: Conduct assessments, track remediation
- System Owners: Remediate findings
- Management: Provide resources, make risk decisions

REVIEW:
Policy reviewed annually.",
        ],

        // 13. System and Communications Protection Policy
        [
            'title' => 'System and Communications Protection Policy',
            'category' => 'System and Communications Protection',
            'frameworks' => 'CMMC,NIST800171,HIPAA,PCI-DSS',
            'description' => 'Defines requirements for protecting information in transit and at rest, network security, and cryptography.',
            'version' => '1.0',
            'content' => "PURPOSE:
To protect information systems and communications from unauthorized access and disclosure.

SCOPE:
All information systems, networks, and communications.

POLICY STATEMENTS:

1. Boundary Protection
   - Firewalls deployed at network boundaries
   - Deny-all, permit-by-exception firewall rules
   - Firewall rules reviewed quarterly
   - Intrusion Detection/Prevention Systems (IDS/IPS) deployed
   - Network segmentation implemented (separate guest, corporate, CUI networks)

2. Transmission Confidentiality
   - Information in transit encrypted using approved protocols:
     * TLS 1.2 or higher for web traffic
     * SSH for remote administration
     * VPN (IPSec or SSL) for remote access
     * S/MIME or PGP for email containing CUI
   - Unencrypted protocols prohibited (HTTP, FTP, Telnet, SNMPv1/v2)

3. Data at Rest Encryption
   - CUI encrypted at rest using:
     * Full disk encryption (BitLocker, FileVault)
     * Database encryption
     * File-level encryption
   - Encryption keys managed securely
   - FIPS 140-2 validated encryption modules used

4. Cryptographic Key Management
   - Encryption keys generated using approved algorithms
   - Keys protected from unauthorized access
   - Keys rotated per vendor guidance or annually
   - Key backups encrypted and stored securely
   - Keys destroyed securely when no longer needed

5. Network Architecture
   - Demilitarized Zone (DMZ) for external-facing systems
   - Internal networks segmented by function/sensitivity
   - Wireless networks separated from wired networks
   - Guest wireless isolated from corporate network

6. Wireless Security
   - WPA3-Enterprise required for corporate wireless
   - WPA2-Personal minimum for guest wireless (separate VLAN)
   - Wireless networks using strong encryption (AES)
   - Rogue access point detection enabled
   - Default SSIDs and passwords changed

7. Voice/Video Communications
   - Voice over IP (VoIP) traffic segregated
   - Video conferencing solutions vetted for security
   - Sensitive discussions not conducted over unsecured channels
   - End-to-end encryption used when available

8. Public Access Systems
   - Public-facing systems in DMZ
   - Web application firewalls (WAF) deployed
   - Secure coding practices followed
   - Public systems undergo security testing before deployment

9. Denial of Service Protection
   - DDoS protection services employed
   - Rate limiting configured
   - Redundancy for critical services

10. Collaborative Computing
    - Screen sharing controlled and monitored
    - Collaborative tools (SharePoint, Teams) configured securely
    - File sharing logged and monitored
    - External collaboration requires approval

RESPONSIBILITIES:
- IT/Network Team: Implement and maintain security controls
- Security Team: Monitor and assess controls
- Users: Use encryption for CUI, report suspicious activity

REVIEW:
Policy reviewed annually.",
        ],

        // 14. System and Information Integrity Policy
        [
            'title' => 'System and Information Integrity Policy',
            'category' => 'System and Information Integrity',
            'frameworks' => 'CMMC,NIST800171,HIPAA,PCI-DSS',
            'description' => 'Establishes requirements for malware protection, system monitoring, and security alerts.',
            'version' => '1.0',
            'content' => "PURPOSE:
To identify, report, and correct information system flaws and protect against malicious code.

SCOPE:
All information systems and software.

POLICY STATEMENTS:

1. Flaw Remediation
   - Vulnerabilities identified through:
     * Vulnerability scans
     * Vendor security bulletins
     * Security advisories
     * Penetration testing
   - Patches and updates applied based on severity:
     * Critical: [15] days
     * High: [30] days
     * Medium: [90] days
     * Low: Next maintenance window
   - Emergency patches applied within [72] hours
   - Patch testing conducted before production deployment when feasible

2. Malicious Code Protection
   - Anti-malware software deployed on all endpoints
   - Anti-malware signatures updated daily (automatically)
   - Real-time scanning enabled
   - Full system scans weekly
   - Email filtering for malicious attachments
   - Web content filtering to block malicious sites
   - Endpoint Detection and Response (EDR) deployed where possible

3. Malware Detection Response
   - Malware detections investigated immediately
   - Infected systems isolated from network
   - Malware removed or system reimaged
   - Root cause determined
   - Similar systems checked for infection

4. Security Alerts and Advisories
   - Security bulletins monitored:
     * US-CERT
     * Vendor security advisories
     * Industry threat intelligence feeds
   - Alerts reviewed and assessed for applicability
   - Action taken based on severity and relevance
   - Advisory response documented

5. System Monitoring
   - Systems monitored for:
     * Security events
     * Performance issues
     * Capacity problems
     * Unusual activity
   - Monitoring tools deployed (SIEM, performance monitoring)
   - Alerts configured for critical events
   - Monitoring reviewed daily

6. Security Functionality Verification
   - Security tools verified operational:
     * Antivirus scanning
     * Firewall rules
     * IDS/IPS
     * Encryption
   - Automated health checks where possible
   - Manual verification monthly
   - Issues remediated immediately

7. Software and Information Integrity
   - Digital signatures verified before installing software
   - Checksums verified for downloads
   - Change detection tools deployed on critical systems
   - Unauthorized changes investigated
   - File integrity monitoring for critical files

8. Spam Protection
   - Email spam filtering deployed
   - Spam quarantined and reviewed
   - Users trained to identify spam/phishing
   - Reporting mechanism for missed spam

9. Information Handling
   - CUI marked and protected per handling requirements
   - Information sharing follows least privilege
   - Data loss prevention (DLP) deployed where feasible
   - Removable media scanning before use

10. Input Validation
    - Applications validate user input
    - SQL injection prevention implemented
    - Cross-site scripting (XSS) prevention implemented
    - Secure coding practices followed

11. Error Handling
    - Error messages do not reveal sensitive information
    - Errors logged for analysis
    - Users see generic error messages
    - Detailed errors available to administrators only

RESPONSIBILITIES:
- IT Department: Deploy and maintain integrity controls, apply patches
- Security Team: Monitor for threats, manage vulnerability remediation
- Users: Keep systems updated, report suspicious activity

REVIEW:
Policy reviewed annually.",
        ],
    ];

    $inserted = 0;
    foreach ($policies as $policy) {
        // Check if policy already exists
        $existing = $db->fetchOne(
            "SELECT id FROM policies WHERE title = ? AND category = ?",
            [$policy['title'], $policy['category']]
        );

        if (!$existing) {
            $db->insert('policies', array_merge($policy, [
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]));
            $inserted++;
        }
    }

    return $inserted;
};
